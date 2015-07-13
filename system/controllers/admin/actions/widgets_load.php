<?php

class actionAdminWidgetsLoad extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $page_id = $this->request->get('page_id');

        if (!is_numeric($page_id)){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $scheme = $widgets_model->getWidgetBindingsScheme($page_id);

        cmsTemplate::getInstance()->renderJSON(array(
            'is_exists' => ($scheme!==false),
            'scheme' => $scheme
        ));

    }

}
