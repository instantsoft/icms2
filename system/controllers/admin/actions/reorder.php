<?php

class actionAdminReorder extends cmsAction {

    public function run($table_name) {

        if (!$this->model->db->isTableExists($table_name)) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $items = $this->request->get('items', []);

        if (!$items) {

            return cmsCore::error404();
        }

        $this->model->reorderByList($table_name, $items);

        $cache_keys = explode('_', str_replace(['{', '}'], '', $table_name));

        cmsCache::getInstance()->clean(implode('.', $cache_keys));

        if ($this->request->isAjax()) {

            return $this->cms_template->renderJSON([
                'error'        => false,
                'success_text' => LANG_CP_ORDER_SUCCESS
            ]);
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();
    }

}
