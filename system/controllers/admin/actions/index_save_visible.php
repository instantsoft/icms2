<?php

class actionAdminIndexSaveVisible extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $items = $this->request->get('items', array());
   
        if (!$items || !is_array($items)) { cmsCore::error404(); }

        foreach ($items as $item) {
            $options['dashboard_visible'][(int)$item['id']] = (int)$item['visible'];
        }

        cmsController::saveOptions('admin', array_merge($this->options, $options));

        $this->halt();

    }

}
