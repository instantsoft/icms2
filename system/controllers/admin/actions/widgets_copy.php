<?php

class actionAdminWidgetsCopy extends cmsAction {

    public function run($binded_id){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidgetBinding($binded_id);
        if (!$widget){
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
        }

        $copy_binded_id = $widgets_model->copyWidget($widget['id']);
        if (!$copy_binded_id){
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
        }

        $bind_widget = $widgets_model->getWidgetBinding($copy_binded_id);

        if($bind_widget['device_types'] && $bind_widget['device_types'] !== array(0) && count($bind_widget['device_types']) < 3){
            foreach ($bind_widget['device_types'] as $dt) {
                $device_types[] = string_lang('LANG_'.$dt.'_DEVICES');
            }
        } else {
            $device_types = false;
        }

        $bind_widget['device_types'] = $device_types;
        $bind_widget['name'] = $bind_widget['widget_title'];

        return $this->cms_template->renderJSON(array(
            'error'  => !(bool) $copy_binded_id,
            'widget' => $bind_widget
        ));

    }

}
