<?php

class actionAdminWidgetsAdd extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $widget_id = $this->request->get('widget_id');
        $page_id = $this->request->get('page_id');
        $position = $this->request->get('position');
        
        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidget($widget_id);

        $binded_id = $widgets_model->addWidgetBinding($widget, $page_id, $position);

        cmsTemplate::getInstance()->renderJSON(array(
            'error' => !(bool)$binded_id, 
            'id' => $binded_id
        ));

    }

}

