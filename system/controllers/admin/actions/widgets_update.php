<?php

class actionAdminWidgetsUpdate extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $this->model_backend_widgets->localizedOff();

        $template_name = $this->request->get('template', '');
        $widget_id     = $this->request->get('id', 0);

        if (!$template_name || !$widget_id) {
            return cmsCore::error404();
        }

        $tpls = cmsCore::getTemplates();
        if (!in_array($template_name, $tpls)) {
            $template_name = cmsConfig::get('template');
        }

        $widget = $this->model_backend_widgets->getWidgetBinding($widget_id);
        if (!$widget) {
            return cmsCore::error404();
        }

        // Чтобы ланг файлы шаблона подгрузились
        $template = new cmsTemplate($template_name);

        cmsCore::loadTemplateLanguage($template->getInheritNames());

        $widget_object = cmsCore::getWidgetObject($widget);

        $form = $this->getWidgetOptionsForm(
            $widget_object->name,
            $widget_object->controller,
            $widget_object->options,
            $template_name,
            $widget_object->isAllowCacheableOption()
        );

        $widget_event_name = 'widget_' . ($widget['controller'] ? $widget['controller'] . '_' : '') . $widget['name'] . '_form';

        list($form, $widget, $widget_object, $template_name) = cmsEventsManager::hook(['widget_form', $widget_event_name], [$form, $widget, $widget_object, $template_name], null, $this->request);

        $widget = $form->parse($this->request, true);

        $errors = $form->validate($this, $widget);

        if (!$errors) {

            $this->model_backend_widgets->updateWidgetBinding($widget_id, $widget);

            $widget = $this->model_backend_widgets->getWidgetBinding($widget_id);

            if ($widget['device_types'] && $widget['device_types'] !== [0] && count($widget['device_types']) < 3) {

                foreach ($widget['device_types'] as $dt) {
                    $device_types[] = string_lang('LANG_' . $dt . '_DEVICES');
                }
            } else {

                $device_types = false;
            }

            $widget['device_type_names'] = $widget['device_types'];
            $widget['device_types']      = $device_types;

            return $this->cms_template->renderJSON([
                'errors'       => false,
                'callback'     => 'widgetUpdated',
                'widget'       => $widget,
                'success_text' => LANG_CP_SAVE_SUCCESS
            ]);
        }

        return $this->cms_template->renderJSON([
            'errors' => $errors
        ]);
    }

}
