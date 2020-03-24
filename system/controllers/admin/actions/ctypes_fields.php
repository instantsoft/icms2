<?php

class actionAdminCtypesFields extends cmsAction {

    public function run($ctype_id = null){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_fields', $ctype['name']);

        return $this->cms_template->render('ctypes_fields', array(
            'ctype' => $ctype,
            'grid' => $grid
        ));

    }

}
