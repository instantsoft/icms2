<?php

class actionFormsIndex extends cmsAction {

    public function run($name){

        if(is_numeric($name)){
            cmsCore::error404();
        }

        $_form_data = $this->getFormData($name);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        if(empty($form_data['options']['available_by_link'])){
            return cmsCore::error404();
        }

        return $this->cms_template->render([
            'form_data' => $form_data,
            'form'      => $form
        ]);
    }

}
