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

        $bind_widget = $widgets_model->getWidgetBinding($binded_id);

        cmsCore::loadWidgetLanguage($bind_widget['name'], $bind_widget['controller']);

        $form = $this->getWidgetOptionsForm($bind_widget['name'], $bind_widget['controller'], false, $bind_widget['template']);
        $data = $form->parse(new cmsRequest($bind_widget));

        $widgets_model->updateWidgetBinding($binded_id, $data);

        $this->cms_template->renderJSON(array(
            'error' => !(bool) $binded_id,
            'name' => $widget['title'],
            'id'    => $binded_id
        ));

    }

}
