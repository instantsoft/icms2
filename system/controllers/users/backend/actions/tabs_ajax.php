<?php

class actionUsersTabsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('tabs');

        $tabs = $this->model->getUsersProfilesTabs();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $tabs);

        $this->halt();

    }

}