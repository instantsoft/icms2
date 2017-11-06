<?php

class actionAdminWidgetsRemove extends cmsAction {

    public function run($id=false){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if (!$id){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->deleteWidget($id);

        $this->halt();

    }

}
