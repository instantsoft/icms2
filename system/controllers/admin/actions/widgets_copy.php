<?php

class actionAdminWidgetsCopy extends cmsAction {

    public function run($widget_id = false){

        if (!$this->request->isAjax() || !$widget_id) {
            cmsCore::error404();
        }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->copyWidget($widget_id);

        $this->cms_template->renderJSON(array(
            'widget' => $widget,
        ));
    }
}
