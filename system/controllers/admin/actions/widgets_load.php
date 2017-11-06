<?php

class actionAdminWidgetsLoad extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $page_id  = $this->request->get('page_id', 0);
        $template = $this->request->get('template', '');

        if (!is_numeric($page_id)){ cmsCore::error404(); }

        $tpls = cmsCore::getTemplates();
        if(!$template || !in_array($template, $tpls)){
            $template = cmsConfig::get('template');
        }

        $widgets_model = cmsCore::getModel('widgets');

        $scheme = $widgets_model->getWidgetBindingsScheme($page_id, $template);

        $this->cms_template->renderJSON(array(
            'is_exists' => ($scheme !== false),
            'scheme'    => $scheme
        ));

    }

}
