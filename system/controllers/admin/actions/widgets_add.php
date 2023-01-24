<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsAdd extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $widget_id     = $this->request->get('widget_id', 0);
        $page_id       = $this->request->get('page_id', 0);
        $position      = $this->request->get('position', '');
        $template_name = $this->request->get('template', '');

        $tpls = cmsCore::getTemplates();
        if (!$template_name || !in_array($template_name, $tpls)) {
            $template_name = cmsConfig::get('template');
        }

        $widget = $this->model_backend_widgets->getWidget($widget_id);
        if (!$widget) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $res = $this->model_backend_widgets->addWidgetBinding($widget, $page_id, $position, $template_name);
        if (!$res) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $widget_bind = $this->model_backend_widgets->localizedOff()->getWidgetBinding($res['id']);
        if (!$widget_bind) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $this->model_backend_widgets->localizedRestore();

        // Чтобы ланг файлы шаблона подгрузились
        $template = new cmsTemplate($template_name);

        cmsCore::loadTemplateLanguage($template->getInheritNames());

        $widget_object = cmsCore::getWidgetObject($widget_bind);

        $form = $this->getWidgetOptionsForm($widget_bind['name'], $widget_bind['controller'], false, $template_name, $widget_object->isAllowCacheableOption());

        $widget_event_name = 'widget_' . ($widget_bind['controller'] ? $widget_bind['controller'] . '_' : '') . $widget_bind['name'] . '_form';

        list($form, $widget_bind, $widget_object, $template_name) = cmsEventsManager::hook(['widget_form', $widget_event_name], [$form, $widget_bind, $widget_object, $template_name], null, $this->request);

        $data = $form->parse(new cmsRequest($widget_bind));

        $this->model_backend_widgets->updateWidgetBinding($res['id'], $data);

        return $this->cms_template->renderJSON([
            'error' => false,
            'name'  => $widget['title'],
            'id'    => $res['id'],
            'bp_id' => $res['bp_id']
        ]);
    }

}
