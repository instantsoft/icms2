<?php

class actionAdminWidgetsTreeAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $controller_name = $this->request->get('controller_name');

        if (!$controller_name){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        cmsCore::loadControllerLanguage($controller_name);

        $pages = $widgets_model->getControllerPages( $controller_name );

        $tree_nodes = array();
        
        if ($pages){
            foreach($pages as $page){
                $tree_nodes[] = array(
                    'title' => $page['title'],
                    'key' => "{$page['controller']}.{$page['id']}",
                    'isLazy' => false
                );
            }
        }

        cmsTemplate::getInstance()->renderJSON($tree_nodes);

    }

}
