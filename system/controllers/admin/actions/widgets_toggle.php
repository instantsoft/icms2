<?php

class actionAdminWidgetsToggle extends cmsAction {

    public function run($id=null){

		if (!$id || !is_numeric($id)){
			$this->cms_template->renderJSON(array(
				'error' => true,
			));
		}

        $i = $this->model->getItemByField('widgets_bind', 'id', $id);

		$is_active = $i['is_enabled'] ? null : 1;

        $this->model->update('widgets_bind', $id, array('is_enabled'=> $is_active));

		$this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => (bool)$is_active
		));

    }

}