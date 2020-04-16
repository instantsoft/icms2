<?php

class actionAdminControllersToggle extends cmsAction {

    public function run($id=false){

		if (!$id){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $item = $this->model->getItemByField('controllers', 'id', $id);
		if (!$item){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$is_pub = $item['is_enabled'] ? 0 : 1;

		$this->model->update('controllers', $id, array(
			'is_enabled' => $is_pub
		));

        $cache = cmsCache::getInstance();

        $cache->clean('controllers');
        $cache->clean('events');

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $is_pub
		));

    }

}
