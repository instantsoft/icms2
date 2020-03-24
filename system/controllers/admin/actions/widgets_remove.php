<?php

class actionAdminWidgetsRemove extends cmsAction {

    public function run($id = false) {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$id) { cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidgetBinding($id);
        if (!$widget) {
            return cmsCore::error404();
        }

        if($widget['image_hint_path']){

            $widget['image_hint_path'] = str_replace($this->cms_config->upload_host.'/', '', $widget['image_hint_path']);

            @unlink($this->cms_config->upload_path.$widget['image_hint_path']);

        }

        $widgets_model->deleteWidget($id);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'success_text' => LANG_CP_WIDGET_REMOVE_SUCCESS
        ));

    }

}
