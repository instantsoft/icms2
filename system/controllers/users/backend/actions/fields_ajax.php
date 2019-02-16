<?php

class actionUsersFieldsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('fields');

        $content_model = cmsCore::getModel('content')->
                            setTablePrefix('')->
                            orderBy('ordering', 'asc');

        $fields = $content_model->getContentFields('{users}', false, false);

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $fields);

        $this->halt();

    }

}
