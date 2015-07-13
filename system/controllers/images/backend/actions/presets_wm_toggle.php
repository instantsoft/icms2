<?php

class actionImagesPresetsWmToggle extends cmsAction {

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
		
		$is_watermark = $preset['is_watermark'] ? false : true;
		
		$images_model->togglePresetIsWatermark($id, $is_watermark);
				
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_watermark
		));

    }

}
