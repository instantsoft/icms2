<?php

class actionAdminWidgetsRemove extends cmsAction {

    public function run($id = false) {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$id) { cmsCore::error404(); }

        $widget = $this->model_backend_widgets->getWidget($id);
        if (!$widget) {
            return cmsCore::error404();
        }

        if ($widget['image_hint_path']) {
            @unlink($this->cms_config->upload_path . $widget['image_hint_path']);
        }

        $widget_before_event_name = 'widget_' . ($widget['controller'] ? $widget['controller'] . '_' : '') . $widget['name'] . '_before_remove';
        $widget_after_event_name  = 'widget_' . ($widget['controller'] ? $widget['controller'] . '_' : '') . $widget['name'] . '_after_remove';

        $widget = cmsEventsManager::hook(['widget_before_remove', $widget_before_event_name], $widget);

        $this->model_backend_widgets->deleteWidget($id);

        $success_text = LANG_CP_WIDGET_REMOVE_SUCCESS;

        list($widget, $success_text) = cmsEventsManager::hook(['widget_after_remove', $widget_after_event_name], [$widget, $success_text]);

        return $this->cms_template->renderJSON(array(
            'error'        => false,
            'success_text' => $success_text
        ));

    }

}
