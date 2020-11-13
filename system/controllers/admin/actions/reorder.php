<?php

class actionAdminReorder extends cmsAction {

    public function run($table_name){

        if (!$this->request->isAjax()){
            return cmsCore::error404();
        }

		if (!$this->model->db->isTableExists($table_name)){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $items = $this->request->get('items', []);
        if (!$items){ cmsCore::error404(); }

        $this->model->reorderByList($table_name, $items);

        $cache_keys = explode('_', $table_name);

        cmsCache::getInstance()->clean(implode('.', $cache_keys));

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'success_text' => LANG_CP_ORDER_SUCCESS
        ));

    }

}
