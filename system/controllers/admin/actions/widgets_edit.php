<?php

class actionAdminWidgetsEdit extends cmsAction {

    public function run($binded_id = false) {

        if (!$binded_id) {
            return cmsCore::error404();
        }

        if($this->request->has('is_iframe')){
            $this->cms_template->setLayout('controllers/admin/widget_edit_layout');
        }

        $template = $this->request->get('template', '');
        if (!$template) {
            return cmsCore::error404();
        }

        $tpls = cmsCore::getTemplates();
        if (!in_array($template, $tpls)) {
            $template = cmsConfig::get('template');
        }

        $widget = cmsCore::getModel('widgets')->getWidgetBinding($binded_id);
        if (!$widget) {
            return cmsCore::error404();
        }

        $widget_object = cmsCore::getWidgetObject($widget);

        $form = $this->getWidgetOptionsForm($widget['name'], $widget['controller'], $widget['options'], $template, $widget_object->isAllowCacheableOption());

        $widget_event_name = 'widget_'.($widget['controller'] ? $widget['controller'].'_' : '').$widget['name'].'_form';

        list($form, $widget, $widget_object, $template) = cmsEventsManager::hook(['widget_form', $widget_event_name], [$form, $widget, $widget_object, $template], null, $this->request);

        return $this->cms_template->render('widgets_settings', array(
            'form'   => $form,
            'widget' => $widget,
            'errors' => false
        ));

    }

}
