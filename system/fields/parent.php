<?php

class fieldParent extends cmsFormField {

    public $title       = LANG_PARSER_PARENT;
    public $is_public   = false;
    public $sql         = 'varchar(1024) NULL DEFAULT NULL';
    public $allow_index = false;
    public $var_type    = 'string';
    public $filter_type = 'str';
    private $input_action = 'bind';

    public function parse($value){

		$parent_items = false;

        if ($value){
			$parent_ctype_name = $this->getParentContentTypeName();
            $parent_items = $this->getParentItems($parent_ctype_name);
        }

        if (!$parent_items){
            return '';
        }

		$result = array();

		foreach($parent_items as $parent_item) {
			$parent_url = href_to($parent_ctype_name, $parent_item['slug'].'.html');
			$result[] = '<a href="'.$parent_url.'">'.$parent_item['title'].'</a>';
		}

		return $result ? implode(', ', $result) : '';

    }

    public function getInput($value) {

		$this->title = $this->element_title;

		$parent_ctype_name = $this->getParentContentTypeName();
		$parent_items = false;

        $author_id = isset($this->item['user_id']) ? $this->item['user_id'] : cmsUser::get('id');

        if ($value){
            $parent_items = $this->getParentItemsByIds($value, $parent_ctype_name);
        } else {
            $parent_items = $this->getParentItems($parent_ctype_name);
        }

		$perm = cmsUser::getPermissionValue($this->item['ctype_name'], 'bind_to_parent');

		$is_allowed_to_bind = ($perm && (
								($perm == 'all_to_all') ||
								($perm == 'own_to_all' && $author_id == cmsUser::get('id')) ||
								($perm == 'own_to_own' && $author_id == cmsUser::get('id'))
							)) || cmsUser::isAdmin();

        $perm = cmsUser::getPermissionValue($this->item['ctype_name'], 'add_to_parent');

        $is_allowed_to_add = ($perm && (($perm == 'to_all') || ($perm == 'to_own'))) || cmsUser::isAdmin();

        if(!$parent_items && !$is_allowed_to_bind){
            return '';
        }

        return cmsTemplate::getInstance()->renderFormField($this->class, array(
			'ctype_name'         => isset($parent_ctype_name) ? $parent_ctype_name : false,
            'child_ctype_name'   => $this->item ? $this->item['ctype_name'] : false,
            'parent_ctype'       => isset($parent_ctype_name) ? cmsCore::getModel('content')->getContentTypeByName($parent_ctype_name) : array(),
            'field'              => $this,
            'input_action'       => $this->input_action,
            'value'              => $value,
            'items'              => $parent_items,
            'is_allowed_to_bind' => $is_allowed_to_bind,
            'is_allowed_to_add'  => $is_allowed_to_add
        ));

    }

    public function getFilterInput($value) {

        $this->input_action = 'select';

        return parent::getFilterInput($value);

    }

    public function applyFilter($model, $values) {

        $ids = $this->idsStringToArray($values);
        if (!$ids) { return parent::applyFilter($model, $values); }

        $model->joinInner('content_relations_bind', 'rr', "rr.child_item_id = i.id AND rr.child_ctype_id = {$this->ctype_id}");

        return $model->filterIn('rr.parent_item_id', $ids);

    }

	private function getParentContentTypeName(){

		preg_match('/parent_([a-z0-9\-\_]+)_id/i', $this->name, $matches);
        if (!$matches || empty($matches[1])){ return false; }
		$parent_ctype_name = $matches[1];

		return $parent_ctype_name;

	}

	private function idsStringToArray($ids_list){

        $ids = array();

        if (!$ids_list) { return $ids; }

        foreach(explode(',', $ids_list) as $id){
            if (!is_numeric($id)) { continue; }
            $ids[] = trim($id);
        }

		return $ids;

	}

    private function getParentItemsByIds($ids_list, $parent_ctype_name){

        $ids = $this->idsStringToArray($ids_list);
        if (!$ids) { return false; }

        $content_model = cmsCore::getModel('content');
        $content_model->filterIn('id', $ids);

        return $content_model->getContentItems($parent_ctype_name);

    }

    private function getParentItems($parent_ctype_name){

		if (!$parent_ctype_name) { return false; }

		if (empty($this->item['id'])) { return false; }

		$content_model = cmsCore::getModel('content');

		$ctypes = $content_model->getContentTypes();

		$parent_ctype = $child_ctype = false;

		foreach($ctypes as $ctype){
			if ($ctype['name'] == $parent_ctype_name){
				$parent_ctype = $ctype;
			}
			if ($ctype['name'] == $this->item['ctype_name']){
				$child_ctype = $ctype;
			}
		}

		if (!$parent_ctype || !$child_ctype) { return false; }

        $filter =  "r.parent_ctype_id = {$parent_ctype['id']} AND ".
                   "r.child_item_id = {$this->item['id']} AND ".
                   "r.child_ctype_id = {$child_ctype['id']} AND ".
                   "r.parent_item_id = i.id";

        $content_model->join('content_relations_bind', 'r', $filter);

		$items = $content_model->getContentItems($parent_ctype_name);

        if ($items){
            foreach($items as $id=>$item){
                $items[$id]['ctype_name'] = $parent_ctype_name;
            }
        }

        return $items;

    }

}
