<?php

class actionAdminContentItemToggle extends cmsAction {

    public function run($ctype_name = false, $item_id = false){

		if (!$ctype_name || !$item_id){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $item = $this->model_content->getContentItem($ctype_name, $item_id);
		if (!$item){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		$is_pub = $item['is_pub'] ? 0 : 1;

		$this->model_content->toggleContentItemPublication($ctype_name, $item_id, $is_pub);

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $is_pub
		));

    }

}
