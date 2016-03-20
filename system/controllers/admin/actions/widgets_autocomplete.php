<?php

class actionAdminWidgetsAutocomplete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $term = $this->request->get('term');

        if (!$term) { cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widgets = $widgets_model->getWidgetsName($term);
        
        $result = array();
        
        cmsCore::loadControllerLanguage('content');

        if ($widgets){
            foreach($widgets as $widget){ 
                $page = $widgets_model->getPage($widget['page_id']);
      
                if(empty($page['title']) && $widget['page_id'] == 0){ $page['title'] = LANG_WP_ALL_PAGES; }
                
                if(empty($page['title']) && $widget['page_id'] == 1){ $page['title'] = LANG_WP_HOME_PAGE; }
                
                $result[] = array(
                    'id'     => $widget['id'],
                    'label'  => $widget['title'].' / '.$page['title'],
                    'value'  => $widget['title'],
                    'page_id'=> $widget['page_id'],
                    'enabled'=> $widget['is_enabled']
                );
            }
        }

        cmsTemplate::getInstance()->renderJSON($result);

    }

}