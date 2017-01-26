<?php

class actionContentItemBindForm extends cmsAction {

    public function run(){

        cmsUser::getInstance();

        $ctype_name       = $this->request->get('ctype_name', '');
        $child_ctype_name = $this->request->get('child_ctype_name', '');
        $item_id          = $this->request->get('id', 0);
        $mode             = $this->request->get('mode', 'childs');
        $input_action     = $this->request->get('input_action', 'bind');
        $selected_ids     = $this->request->get('selected', '');

        if (!$ctype_name || !$child_ctype_name){
            cmsCore::error404();
        }

        $is_allowed_to_add = cmsUser::isAllowed($child_ctype_name, 'add_to_parent');
        $is_allowed_to_bind = cmsUser::isAllowed($child_ctype_name, 'bind_to_parent');
        $is_allowed_to_unbind = cmsUser::isAllowed($child_ctype_name, 'bind_off_parent');

        if ($mode != 'unbind' && (!$is_allowed_to_add && !$is_allowed_to_bind)) {
            cmsCore::error404();
        }

        if ($mode == 'unbind' && !$is_allowed_to_unbind) {
            cmsCore::error404();
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

		$item = array('id' => 0);

		if ($item_id){
			if ($mode == 'childs' || $mode == 'unbind'){
				$item = $this->model->getContentItem($ctype_name, $item_id);
			} else {
				$item = $this->model->getContentItem($child_ctype_name, $item_id);
			}
			if (!$item) { cmsCore::error404(); }
		}

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);
        if (!$child_ctype) { cmsCore::error404(); }

		$relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id']);
		if (!$relation) { cmsCore::error404(); }

		if ($mode == 'childs' || $mode == 'unbind'){
			$fields = $this->model->getContentFields($child_ctype_name);
		}

		if ($mode == 'parents'){
			$fields = $this->model->getContentFields($ctype_name);
		}

		$filter_fields = array();

		foreach($fields as $field){
			if ($field['handler']->filter_type == 'str'){
				$filter_fields[$field['name']] = $field['title'];
			}
		}

		$filter_fields['id'] = 'ID';

        return $this->cms_template->render('item_bind_form', array(
			'mode'          => $mode,
            'ctype'         => $ctype,
            'child_ctype'   => $child_ctype,
            'item'          => $item,
            'input_action'  => $input_action,
            'selected_ids'  => $selected_ids,
            'filter_fields' => $filter_fields
        ));

    }

}
