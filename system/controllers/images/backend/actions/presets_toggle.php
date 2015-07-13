<?php

class actionImagesPresetsToggle extends cmsAction {

    public function run($id=false){

		if (!$id){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}
		
        $images_model = cmsCore::getModel('images');

        $preset = $images_model->getPreset($id);

		if (!$preset){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}
		
		$is_square = $preset['is_square'] ? false : true;
		
		$images_model->togglePresetIsSquare($id, $is_square);
				
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_square
		));

    }

}
