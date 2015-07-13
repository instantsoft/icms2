<?php

class actionAdminContentItemToggle extends cmsAction {

    public function run($ctype_name=false, $item_id=false){

		if (!$ctype_name || !$item_id){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}
		
        $content_model = cmsCore::getModel('content');

        $item = $content_model->getContentItem($ctype_name, $item_id);

		if (!$item){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}
		
		$is_pub = $item['is_pub'] ? false : true;
		
		$content_model->toggleContentItemPublication($ctype_name, $item_id, $is_pub);
				
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_pub
		));

    }

}
