<?php

class actionContentItemChildsView extends cmsAction {

    public function run($ctype, $item, $child_ctype_name, $childs){

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);

        if (!$child_ctype) {
            cmsCore::error404();
        }

        $relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id']);

        $this->model->filterEqual("parent_{$ctype['name']}_id", $item['id']);

        if (!empty($relation['options']['limit'])){
            $child_ctype['options']['limit'] = $relation['options']['limit'];
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $child_ctype['options']['list_show_filter'] = false;
        }

        $html = $this->renderItemsList($child_ctype, href_to($ctype['name'], $item['slug'], "view-{$child_ctype_name}"));

        $seo_title = empty($relation['seo_title']) ? $child_ctype['type'] . ' - ' . $item['title'] : string_replace_keys_values($relation['seo_title'], $item);
        $seo_keys = empty($relation['seo_keys']) ? '' : string_replace_keys_values($relation['seo_keys'], $item);
        $seo_desc = empty($relation['seo_desc']) ? '' : string_replace_keys_values($relation['seo_desc'], $item);

        return $this->cms_template->render('item_childs_view', array(
            'ctype' => $ctype,
            'child_ctype' => $child_ctype,
            'item' => $item,
            'childs' => $childs,
            'html' => $html,
            'relation' => $relation,
            'seo_title' => $seo_title,
            'seo_keys' => $seo_keys,
            'seo_desc' => $seo_desc
        ));

	}



}
