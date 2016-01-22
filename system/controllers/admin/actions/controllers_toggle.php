<?php

class actionAdminControllersToggle extends cmsAction {

    public function run($id=false){

		if (!$id){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));
		}

        $item = $this->model->getItemByField('controllers', 'id', $id);

		if (!$item){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));
		}

		$is_pub = $item['is_enabled'] ? false : true;

		$this->model->update('controllers', $id, array(
			'is_enabled' => $is_pub
		));

        $cache = cmsCache::getInstance();

        $cache->clean('controllers');
        $cache->clean('events');

		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_pub
		));

    }

}