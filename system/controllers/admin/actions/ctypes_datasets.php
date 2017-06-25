<?php

class actionAdminCtypesDatasets extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_datasets');

        if ($this->request->isAjax()) {

            $content_model->orderBy('ordering', 'asc');

            $datasets = $content_model->getContentDatasets($ctype_id);

            $this->cms_template->renderGridRowsJSON($grid, $datasets);

            $this->halt();

        }

        return $this->cms_template->render('ctypes_datasets', array(
            'ctype' => $ctype,
            'grid'  => $grid
        ));

    }

}
