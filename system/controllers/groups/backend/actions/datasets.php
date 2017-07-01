<?php

class actionGroupsDatasets extends cmsAction {

    public function run(){

        $admin = cmsCore::getController('admin');

        $grid = $admin->loadDataGrid('ctype_datasets');

        if ($this->request->isAjax()) {

            $content_model = cmsCore::getModel('content');

            $content_model->orderBy('ordering', 'asc');

            $datasets = $content_model->getContentDatasets('groups');

            $this->cms_template->renderGridRowsJSON($grid, $datasets);

            $this->halt();

        }

        return $this->cms_template->render('backend/datasets', array(
            'grid' => $grid
        ));

    }

}
