<?php

class actionAdminCtypesDatasetsToggle extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { 
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));			
		}

        $content_model = cmsCore::getModel('content');

		$dataset = $content_model->getContentDataset($dataset_id);
		
		$is_visible = $dataset['is_visible'] ? false : true;
		
		$content_model->toggleContentDatasetVisibility($dataset_id, $is_visible);
		
		cmsTemplate::getInstance()->renderJSON(array(
			'error' => false,
			'is_on' => $is_visible
		));

    }

}
