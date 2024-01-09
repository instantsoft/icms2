<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsEdit extends cmsAction {

    public function run($binded_id = false) {

        if (!$binded_id) {
            return cmsCore::error404();
        }

        if ($this->request->has('is_iframe')) {
            $this->cms_template->setLayout('controllers/admin/widget_edit_layout');
        }

        $template_name = $this->request->get('template', '');
        if (!$template_name) {
            return cmsCore::error404();
        }

        $tpls = cmsCore::getTemplates();
        if (!in_array($template_name, $tpls)) {
            $template_name = cmsConfig::get('template');
        }

        $widget = $this->model_backend_widgets->localizedOff()->getWidgetBinding($binded_id);
        if (!$widget) {
            return cmsCore::error404();
        }

        $this->model_backend_widgets->localizedRestore();

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

        return $this->cms_template->render('widgets_settings', [
            'form'   => $form,
            'widget' => $widget,
            'errors' => false
        ]);
    }

}
