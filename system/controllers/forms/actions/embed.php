<?php

class actionFormsEmbed extends cmsAction {

    public function run($hash){

        if(!$this->isAllowEmbed() || is_numeric($hash)){
            cmsCore::error404();
        }

        $_form_data = $this->getFormData($hash);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        // Здесь виджеты не нужны
        $this->cms_template->widgets_rendered = true;

        $this->cms_template->setLayout('controllers/forms/embed_layout');
        $this->cms_template->setLayoutParams([
            'form_data' => $form_data
        ]);

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
