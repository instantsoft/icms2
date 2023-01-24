<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsCopy extends cmsAction {

    public function run($bp_id) {

        if (!$this->request->isAjax() || !$bp_id) {
            return cmsCore::error404();
        }

        $this->model_backend_widgets->localizedOff();

        $copy = $this->model_backend_widgets->copyWidgetByPage($bp_id);
        if (!$copy) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $widget_bind  = $this->model_backend_widgets->getWidgetBinding($copy['id']);
        $binding_page = $this->model_backend_widgets->getWidgetBindPage($copy['bp_id']);

        if ($widget_bind['device_types'] && $widget_bind['device_types'] !== [0] && count($widget_bind['device_types']) < 3) {

            foreach ($widget_bind['device_types'] as $dt) {
                $device_types[] = string_lang('LANG_' . $dt . '_DEVICES');
            }
        } else {
            $device_types = false;
        }

        $widget_bind['device_type_names'] = $widget_bind['device_types'];
        $widget_bind['device_types']      = $device_types;
        $widget_bind['name']              = $widget_bind['widget_title'];

        $widget_bind['bind_id']    = $widget_bind['id'];
        $widget_bind['id']         = $binding_page['id'];
        $widget_bind['position']   = $binding_page['position'];
        $widget_bind['is_enabled'] = $binding_page['is_enabled'];

        return $this->cms_template->renderJSON([
            'error'  => false,
            'widget' => $widget_bind
        ]);
    }

}
