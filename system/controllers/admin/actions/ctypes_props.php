<?php

class actionAdminCtypesProps extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $cats = $this->model_content->getSubCategories($ctype['name']);

        $props = $this->model_content->orderBy('title')->getContentProps($ctype['name']);

        $grid = $this->loadDataGrid('ctype_props', $this->cms_template->href_to('ctypes', array('props_reorder', $ctype['name'])));

        return $this->cms_template->render('ctypes_props', array(
            'ctype' => $ctype,
            'cats'  => $cats,
            'props' => $props,
            'grid'  => $grid
        ));

    }

}
