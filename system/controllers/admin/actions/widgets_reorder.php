<?php

class actionAdminWidgetsReorder extends cmsAction {

    public function run(){

        $position = $this->request->get('position');
        $items = $this->request->get('items');
        $page_id = $this->request->get('page_id');

        if (!$items){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->reorderWidgetsBindings($position, $items, $page_id);

        $this->halt();

    }

}
