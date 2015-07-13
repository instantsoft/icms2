<?php

class actionAdminMenuItemsAjax extends cmsAction {

    public function run($menu_id, $parent_id){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        $grid = $this->loadDataGrid('menu_items');

        $items = $menu_model->getMenuItems($menu_id, $parent_id);

        $total = $items ? 1 : 0;

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $items, $total);

        $this->halt();

    }

}
