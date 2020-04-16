<?php

class actionAdminWidgetsRemove extends cmsAction {

    public function run($id = false) {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$id) { cmsCore::error404(); }

        $widget = $this->model_widgets->getWidget($id);
        if (!$widget) {
            return cmsCore::error404();
        }

        if($widget['image_hint_path']){
            @unlink($this->cms_config->upload_path.$widget['image_hint_path']);
        }

        $this->model_widgets->deleteWidget($id);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'success_text' => LANG_CP_WIDGET_REMOVE_SUCCESS
        ));

    }

}
