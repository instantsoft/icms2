<?php

class actionUsersItemChildsView extends cmsAction {

    public $lock_explicit_call = true;

    public function run($ctype, $item, $childs, $content_controller, $fields, $child_target){

        if(!empty($childs['tabs'][$this->name]['relation_id'])){
            $relation = $childs['relations'][$childs['tabs'][$this->name]['relation_id']];
        } else {
            cmsCore::error404();
        }

        if (!in_array($relation['layout'], array('tab', 'hidden'))) {
            cmsCore::error404();
        }

        $filter =   "r.parent_ctype_id = '{$ctype['id']}' AND ".
                    "r.parent_item_id = '{$item['id']}' AND ".
                    'r.child_ctype_id IS NULL AND '.
                    "r.child_item_id = i.id AND r.target_controller = '{$this->name}'";

        $this->model->joinInner('content_relations_bind', 'r', $filter);

        if (!empty($relation['options']['limit'])){
            $this->setOption('limit', $relation['options']['limit']);
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $this->setOption('is_filter', false);
        }

        $html = $this->renderProfilesList(href_to($ctype['name'], $item['slug'].'/view-'.$this->name));

        $item_seo = $content_controller->prepareItemSeo($item, $fields, $ctype);

        $seo_title = empty($relation['seo_title']) ? LANG_USERS . ' - ' . $item['title'] : string_replace_keys_values_extended($relation['seo_title'], $item_seo);
        $seo_keys  = empty($relation['seo_keys']) ? '' : string_replace_keys_values_extended($relation['seo_keys'], $item_seo);
        $seo_desc  = empty($relation['seo_desc']) ? '' : string_get_meta_description(string_replace_keys_values_extended($relation['seo_desc'], $item_seo));

        $this->cms_template->setContext($content_controller);

        return $this->cms_template->render('item_childs_view', array(
            'ctype'       => $ctype,
            'child_ctype' => array('name' => $this->name, 'title' => LANG_USERS),
            'item'        => $item,
            'childs'      => $childs,
            'html'        => $html,
            'relation'    => $relation,
            'seo_title'   => $seo_title,
            'seo_keys'    => $seo_keys,
            'seo_desc'    => $seo_desc
        ));

	}

}
