<?php

class actionContentItemChildsView extends cmsAction {

    public function run($ctype, $item, $child_ctype_name, $childs){

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);

        if (!$child_ctype) {
            cmsCore::error404();
        }

        $relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id']);

        if (!$relation) {
            cmsCore::error404();
        }

        if (!in_array($relation['layout'], array('tab', 'hidden'))) {
            cmsCore::error404();
        }

        $filter =   "r.parent_ctype_id = {$ctype['id']} AND ".
                    "r.parent_item_id = {$item['id']} AND ".
                    "r.child_ctype_id = {$child_ctype['id']} AND ".
                    "r.child_item_id = i.id";

        $this->model->joinInner('content_relations_bind', 'r', $filter);

        if (!empty($relation['options']['limit'])){
            $child_ctype['options']['limit'] = $relation['options']['limit'];
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $child_ctype['options']['list_show_filter'] = false;
        }

        $html = $this->renderItemsList($child_ctype, href_to($ctype['name'], $item['slug'].'/view-'.$child_ctype_name));

        $seo_title = empty($relation['seo_title']) ? $child_ctype['title'] . ' - ' . $item['title'] : string_replace_keys_values($relation['seo_title'], $item);
        $seo_keys  = empty($relation['seo_keys']) ? '' : string_replace_keys_values($relation['seo_keys'], $item);
        $seo_desc  = empty($relation['seo_desc']) ? '' : string_get_meta_description(string_replace_keys_values($relation['seo_desc'], $item));

        return $this->cms_template->render('item_childs_view', array(
            'ctype'       => $ctype,
            'child_ctype' => $child_ctype,
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
