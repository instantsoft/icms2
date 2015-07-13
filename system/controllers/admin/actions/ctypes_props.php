<?php

class actionAdminCtypesProps extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $cats = $content_model->getSubCategories($ctype['name']);

        $props = $content_model->orderBy('title')->getContentProps($ctype['name']);

        $grid = $this->loadDataGrid('ctype_props');

        return cmsTemplate::getInstance()->render('ctypes_props', array(
            'ctype' => $ctype,
            'cats' => $cats,
            'props' => $props,
            'grid' => $grid,
        ));

    }

}
