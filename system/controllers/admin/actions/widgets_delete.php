<?php

class actionAdminWidgetsDelete extends cmsAction {

    public function run($binded_id=false){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$binded_id){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->deleteWidgetBinding($binded_id);

        $this->halt();

    }

}
