<?php

class actionGroupsFields extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('fields');

        if ($this->request->isAjax()) {

            $content_model = cmsCore::getModel('content')->
                                setTablePrefix('')->
                                orderBy('ordering', 'asc');

            $fields = $content_model->getContentFields('groups', false, false);

            $this->cms_template->renderGridRowsJSON($grid, $fields);

            $this->halt();

        }

        return $this->cms_template->render('backend/fields', array(
            'grid' => $grid
        ));

    }

}
