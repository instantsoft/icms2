<?php

class actionAdminIndexSaveOrder extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $items = $this->request->get('items', array());
        if (!$items || !is_array($items)) { cmsCore::error404(); }

        foreach ($items as $order_id => $block_id) {
            $options['dashboard_order'][(int)$block_id] = (int)$order_id;
        }

        cmsController::saveOptions('admin', array_merge($this->options, $options));

        $this->halt();

    }

}
