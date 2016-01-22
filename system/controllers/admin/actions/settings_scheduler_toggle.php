<?php

class actionAdminSettingsSchedulerToggle extends cmsAction {

    public function run($id=false){
        
        if (!$id) { 
            
                cmsTemplate::getInstance()->renderJSON(array(
                        'error' => true,
                    
                ));			
		}
		
        $task = $this->model->getSchedulerTask($id);	

        $is_active = $task['is_active'] ? false : true;

        $this->model->toggleSchedulerPublication($id, $is_active);


        cmsTemplate::getInstance()->renderJSON(array(
                'error' => false,
                'is_on' => $is_active
        ));

    }

}
