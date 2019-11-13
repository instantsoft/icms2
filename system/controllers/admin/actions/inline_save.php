<?php

class actionAdminInlineSave extends cmsAction {

    public function run($table = null, $item_id = null) {

        header('X-Frame-Options: DENY');

        if (!$this->request->isAjax()) { return cmsCore::error404(); }

		if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp('/^([a-z0-9\_{}]*)$/', urldecode($table)) !== true){
			return $this->cms_template->renderJSON(array(
				'error' => LANG_ERROR.' #validate'
			));
		}

        $data = $this->request->get('data', array());
        if(!$data){
			return $this->cms_template->renderJSON(array(
				'error' => LANG_ERROR.' #empty data'
			));
        }

		if (!$this->model->db->isTableExists($table)){
			return $this->cms_template->renderJSON(array(
				'error' => LANG_ERROR.' #table'
			));
		}

        $i = $this->model->getItemByField($table, 'id', $item_id);
		if (!$i){
			return $this->cms_template->renderJSON(array(
				'error' => LANG_ERROR.' #404'
			));
		}

        $_data = array();

        foreach ($data as $field => $value) {
            if(!array_key_exists($field, $i)){
                unset($data[$field]);
            } else {
                $_data[$field] = htmlspecialchars($value);
            }
        }

        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save', array($data, $_data, $i));
        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save_'.str_replace(['{','}'], '', $table), array($data, $_data, $i));

        if(empty($data)){
			return $this->cms_template->renderJSON(array(
				'error' => LANG_ERROR.' #empty data'
			));
        }

		$this->model->update($table, $item_id, $data);

		return $this->cms_template->renderJSON(array(
			'error'  => false,
			'info'   => LANG_SUCCESS_MSG,
			'values' => $_data
		));

    }

}
