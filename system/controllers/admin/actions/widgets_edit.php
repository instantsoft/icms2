<?php

class actionAdminWidgetsEdit extends cmsAction {

    public function run($binded_id=false){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$binded_id){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widget = $widgets_model->getWidgetBinding($binded_id);
        if (!$widget){ cmsCore::error404(); }

        if(!$widget['tpl_wrap']){
            $widget['tpl_wrap'] = 'wrapper';
        }

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        $form = $this->getWidgetOptionsForm($widget['name'], $widget['controller'], $widget['options'], $widget['template']);

        return $this->cms_template->render('widgets_settings', array(
            'form'   => $form,
            'widget' => $widget,
            'errors' => false
        ));

    }

}
