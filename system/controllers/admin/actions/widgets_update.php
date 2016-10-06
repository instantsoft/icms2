<?php

class actionAdminWidgetsUpdate extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$this->request->has('id')){ cmsCore::error404(); }

        $template = cmsTemplate::getInstance();
        $widgets_model = cmsCore::getModel('widgets');

        $widget_id = $this->request->get('id', 0);

        $widget = $widgets_model->getWidgetBinding($widget_id);

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        $form = cmsCore::getWidgetOptionsForm($widget['name'], $widget['controller'], false, $widget['template']);

        $widget = $form->parse($this->request, true);

        $errors = $form->validate($this,  $widget);

        if (!$errors){

            $widgets_model->updateWidgetBinding($widget_id, $widget);

            $template->renderJSON(array(
                'errors' => false,
                'callback' => 'widgetUpdated'
            ));

        }

        if ($errors){

            $template->renderJSON(array(
                'errors' => $errors
            ));

        }

    }

}
