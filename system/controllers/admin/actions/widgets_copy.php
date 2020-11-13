<?php

class actionAdminWidgetsCopy extends cmsAction {

    public function run($bp_id) {

        if (!$this->request->isAjax() || !$bp_id) {
            return cmsCore::error404();
        }

        $widgets_model = cmsCore::getModel('widgets');

        $copy = $widgets_model->copyWidgetByPage($bp_id);
        if (!$copy) {
            return $this->cms_template->renderJSON(array('error' => true));
        }

        $widget_bind  = $widgets_model->getWidgetBinding($copy['id']);
        $binding_page = $widgets_model->getWidgetBindPage($copy['bp_id']);

        if ($widget_bind['device_types'] && $widget_bind['device_types'] !== array(0) && count($widget_bind['device_types']) < 3) {

            foreach ($widget_bind['device_types'] as $dt) {
                $device_types[] = string_lang('LANG_' . $dt . '_DEVICES');
            }

        } else {
            $device_types = false;
        }

        $widget_bind['device_type_names'] = $widget_bind['device_types'];
        $widget_bind['device_types'] = $device_types;
        $widget_bind['name']         = $widget_bind['widget_title'];

        $widget_bind['bind_id']    = $widget_bind['id'];
        $widget_bind['id']         = $binding_page['id'];
        $widget_bind['position']   = $binding_page['position'];
        $widget_bind['is_enabled'] = $binding_page['is_enabled'];

        return $this->cms_template->renderJSON(array(
            'error'  => false,
            'widget' => $widget_bind
        ));

    }

}
