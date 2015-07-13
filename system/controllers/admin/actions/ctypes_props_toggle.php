<?php

class actionAdminCtypesPropsToggle extends cmsAction {

    public function run($ctype_id, $prop_id){

        if (!$ctype_id || !$prop_id) { 
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
		$prop = $content_model->getContentProp($ctype['name'], $prop_id);
		
		$is_in_filter = $prop['is_in_filter'] ? false : true;
		
		$content_model->toggleContentPropFilter($ctype['name'], $prop_id, $is_in_filter);
		
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_in_filter
		));

    }

}
