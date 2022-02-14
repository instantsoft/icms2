<?php

class actionFormsView extends cmsAction {

    public function run($hash){

        if (!$this->request->isAjax()) { return cmsCore::error404(); }

        if(is_numeric($hash)){
            return cmsCore::error404();
        }

        $_form_data = $this->getFormData($hash);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        // Здесь виджеты не нужны
        $this->cms_template->widgets_rendered = true;

        $submited_data = $this->getSavedUserFormData($form_data['id']);

        if($submited_data && !empty($form_data['options']['hide_after_submit'])){
            $this->halt();
        }

        return $this->cms_template->render('form_view', [
            'form_data' => $form_data,
            'form'      => $form
        ]);
    }

}
