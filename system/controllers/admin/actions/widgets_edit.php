<?php

class actionAdminWidgetsEdit extends cmsAction {

    public function run($binded_id = false) {

        if (!$this->request->isAjax() || !$binded_id) {
            return cmsCore::error404();
        }

        $template = $this->request->get('template', '');
        if (!$template) {
            return cmsCore::error404();
        }

        $widget = cmsCore::getModel('widgets')->getWidgetBinding($binded_id);
        if (!$widget) {
            return cmsCore::error404();
        }

        if (!$widget['tpl_wrap']) {
            $widget['tpl_wrap'] = 'wrapper';
        }

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        $form = $this->getWidgetOptionsForm($widget['name'], $widget['controller'], $widget['options'], $template);

        return $this->cms_template->render('widgets_settings', array(
            'form'   => $form,
            'widget' => $widget,
            'errors' => false
        ));

    }

}
