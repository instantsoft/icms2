<?php

class actionAdminToggleItem extends cmsAction {

    public function run($item_id = 0, $table = '', $field = 'is_pub') {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $backend_request = clone $this->request;
        $backend_request->setContext(cmsRequest::CTX_AJAX);

        return $this->loadControllerBackend('admin', $backend_request)->actionToggleItem($item_id, $table, $field);
    }

}
