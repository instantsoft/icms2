<?php

class actionAdminWidgetsUpdate extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$this->request->has('id')){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widget_id = $this->request->get('id', 0);

        $widget = $widgets_model->getWidgetBinding($widget_id);
        if (!$widget){ cmsCore::error404(); }

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        $form = $this->getWidgetOptionsForm($widget['name'], $widget['controller'], false, $widget['template']);

        $widget = $form->parse($this->request, true);

        $errors = $form->validate($this,  $widget);

        if (!$errors){

            $widgets_model->updateWidgetBinding($widget_id, $widget);

            $widget = $widgets_model->getWidgetBinding($widget_id);

            if($widget['device_types'] && $widget['device_types'] !== array(0) && count($widget['device_types']) < 3){
                foreach ($widget['device_types'] as $dt) {
                    $device_types[] = string_lang('LANG_'.$dt.'_DEVICES');
                }
            } else {
                $device_types = false;
            }
            $widget['device_types'] = $device_types;

            return $this->cms_template->renderJSON(array(
                'errors'   => false,
                'callback' => 'widgetUpdated',
                'widget'   => $widget
            ));

        }

        return $this->cms_template->renderJSON(array(
            'errors' => $errors
        ));

    }

}
