<?php

class actionAdminWidgetsAdd extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $widget_id = $this->request->get('widget_id', 0);
        $page_id   = $this->request->get('page_id', 0);
        $position  = $this->request->get('position', '');
        $template  = $this->request->get('template', '');

        $tpls = cmsCore::getTemplates();
        if (!$template || !in_array($template, $tpls)) {
            $template = cmsConfig::get('template');
        }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidget($widget_id);
        if (!$widget) {
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $res = $widgets_model->addWidgetBinding($widget, $page_id, $position, $template);
        if (!$res) {
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $widget_bind = $widgets_model->getWidgetBinding($res['id']);
        if (!$widget_bind) {
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $widget_object = cmsCore::getWidgetObject($widget_bind);

        $form = $this->getWidgetOptionsForm($widget_bind['name'], $widget_bind['controller'], false, $template, $widget_object->isAllowCacheableOption());
        $data = $form->parse(new cmsRequest($widget_bind));

        $widgets_model->updateWidgetBinding($res['id'], $data);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'name'  => $widget['title'],
            'id'    => $res['id'],
            'bp_id' => $res['bp_id']
        ));

    }

}
