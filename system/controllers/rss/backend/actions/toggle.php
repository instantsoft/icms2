<?php

class actionRssToggle extends cmsAction {

    public function run($id=false){  
        
        if (!$id) { 
            
            cmsTemplate::getInstance()->renderJSON(array(
                    'error' => true,

            ));			
            }
                
        $rss_model = cmsCore::getModel('rss');
                
        $feed = $rss_model->getFeed($id); 
            
        $is_active = $feed['is_enabled'] ? false : true;

        $rss_model->toggleFeedEnable($id, $is_active);


        cmsTemplate::getInstance()->renderJSON(array(
                'error' => false,
                'is_on' => $is_active
        ));

    }

}