<?php

class actionAdminInlineSave extends cmsAction {

    public function run($table=null, $item_id=null){

        header('X-Frame-Options: DENY');

        if (!$this->request->isAjax()) { cmsCore::error404(); }

		if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp('/^([a-z0-9\_{}]*)$/', urldecode($table)) !== true){
			$this->cms_template->renderJSON(array(
				'error' => LANG_ERROR
			));
		}

        $data = $this->request->get('data', array());
        if(!$data){
			$this->cms_template->renderJSON(array(
				'error' => LANG_ERROR
			));
        }

        $i = $this->model->getItemByField($table, 'id', $item_id);
		if (!$i){
			$this->cms_template->renderJSON(array(
				'error' => LANG_ERROR
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

        if(empty($data)){
			$this->cms_template->renderJSON(array(
				'error' => LANG_ERROR
			));
        }

		$this->model->update($table, $item_id, $data);

		$this->cms_template->renderJSON(array(
			'error'  => false,
			'values' => $_data
		));

    }

}
