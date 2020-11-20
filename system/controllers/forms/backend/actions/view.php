<?php

class actionFormsView extends cmsAction {

    public function run($id){

        $_form_data = cmsCore::getController('forms', $this->request)->getFormData($id);

        if($_form_data === false){
            return cmsCore::error404();
        }

        list($form, $form_data) = $_form_data;

        return $this->cms_template->render([
            'form_data' => $form_data,
            'form'      => $form,
            'errors'    => false
        ]);
    }

}
