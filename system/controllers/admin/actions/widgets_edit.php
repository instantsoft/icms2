<?php

class actionAdminWidgetsEdit extends cmsAction {

    public function run($binded_id=false){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$binded_id){ cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidgetBinding($binded_id);

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        $form = cmsCore::getWidgetOptionsForm($widget['name'], $widget['controller'], $widget['options']);

        return $template->render('widgets_settings', array(
            'form' => $form,
            'widget' => $widget,
            'errors' => false
        ));

    }

}

