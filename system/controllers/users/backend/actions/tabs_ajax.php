<?php

class actionUsersTabsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('tabs');

        $users_model = cmsCore::getModel('users');

        $tabs = $users_model->getUsersProfilesTabs();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $tabs);

        $this->halt();

    }

}
