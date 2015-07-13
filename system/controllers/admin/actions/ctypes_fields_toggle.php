<?php

class actionAdminCtypesFieldsToggle extends cmsAction {

    public function run($mode, $ctype_id, $field_id){

        if (!in_array($mode, array('list', 'item')) || !$ctype_id || !$field_id) { 
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
		$field = $content_model->getContentField($ctype['name'], $field_id);
		
		$visibility_field = $mode=='list' ? 'is_in_list' : 'is_in_item';
		
		$is_visible = $field[$visibility_field] ? false : true;
		
		$content_model->toggleContentFieldVisibility($ctype['name'], $field_id, $visibility_field, $is_visible);
		
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_visible
		));

    }

}
