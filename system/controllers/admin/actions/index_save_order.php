<?php

class actionAdminIndexSaveOrder extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $items = $this->request->get('items', []);
        if (!$items || !is_array($items)) {
            return cmsCore::error404();
        }

        $options = [];

        foreach ($items as $order_id => $name) {
            $options['dashboard_order'][$name] = (int) $order_id;
        }

        cmsController::saveOptions('admin', array_merge($this->options, $options));

        return $this->cms_template->renderJSON([
            'error'        => false,
            'success_text' => LANG_CP_ORDER_SUCCESS
        ]);
    }

}
