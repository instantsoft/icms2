<?php

class actionFormsEmbed extends cmsAction {

    public function run($hash){

        if(!$this->isAllowEmbed() || is_numeric($hash)){
            cmsCore::error404();
        }

        $_form_data = $this->model->getFormData($hash);

        if($_form_data === false){
            return cmsCore::error404();
        }

        // Здесь виджеты не нужны
        $this->cms_template->widgets_rendered = true;

        $this->cms_template->setLayout('controllers/forms/embed_layout');

        list($form, $form_data) = $_form_data;

        return $this->cms_template->render('form_view', [
            'form_data' => $form_data,
            'form'      => $form
        ]);

    }

}
