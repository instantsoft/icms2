<?php

class actionAdminCtypesFields extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_fields');

        return cmsTemplate::getInstance()->render('ctypes_fields', array(
            'ctype' => $ctype,
            'grid' => $grid
        ));

    }

}
