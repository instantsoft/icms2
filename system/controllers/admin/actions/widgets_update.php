<?php

class actionAdminWidgetsUpdate extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $template  = $this->request->get('template', '');
        $widget_id = $this->request->get('id', 0);

        if (!$template || !$widget_id) {
            return cmsCore::error404();
        }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidgetBinding($widget_id);
        if (!$widget) {
            return cmsCore::error404();
        }

        $widget_object = cmsCore::getWidgetObject($widget);

        $form = $this->getWidgetOptionsForm($widget['name'], $widget['controller'], false, $template, $widget_object->isAllowCacheableOption());

        $widget = $form->parse($this->request, true);

        $errors = $form->validate($this, $widget);

        if (!$errors) {

            $widgets_model->updateWidgetBinding($widget_id, $widget);

            $widget = $widgets_model->getWidgetBinding($widget_id);

            if ($widget['device_types'] && $widget['device_types'] !== array(0) && count($widget['device_types']) < 3) {

                foreach ($widget['device_types'] as $dt) {
                    $device_types[] = string_lang('LANG_' . $dt . '_DEVICES');
                }

            } else {

                $device_types = false;

            }

            $widget['device_type_names'] = $widget['device_types'];
            $widget['device_types'] = $device_types;

            return $this->cms_template->renderJSON(array(
                'errors'   => false,
                'callback' => 'widgetUpdated',
                'widget'   => $widget,
                'success_text' => LANG_CP_SAVE_SUCCESS
            ));

        }

        return $this->cms_template->renderJSON(array(
            'errors' => $errors
        ));

    }

}
