<?php

class actionAdminMenuItemsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items');

        if (!$items){ cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        $menu_model->reorderMenuItems($items);

        $this->redirectBack();

    }

}
