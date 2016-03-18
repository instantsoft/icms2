<?php

class actionAdminWidgetsAdd extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $widget_id = $this->request->get('widget_id', 0);
        $page_id   = $this->request->get('page_id', 0);
        $position  = $this->request->get('position', '');
        $template  = $this->request->get('template', '');

        $tpls = cmsCore::getTemplates();
        if(!$template || !in_array($template, $tpls)){
            $template = cmsConfig::get('template');
        }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidget($widget_id);

        $binded_id = $widgets_model->addWidgetBinding($widget, $page_id, $position, $template);

        cmsTemplate::getInstance()->renderJSON(array(
            'error' => !(bool) $binded_id,
            'id'    => $binded_id
        ));

    }

}