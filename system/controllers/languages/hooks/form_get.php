<?php

class onLanguagesFormGet extends cmsAction {

    public function run($data){

        list($form_context, $form, $params) = $data;

        list($controller_name, $form_name) = $form_context;

        if(empty($this->options['sources'][$controller_name][$form_name])){
            return $data;
        }

        $this->enableMultilanguageFormFields($form);

        return [$form_context, $form, $params];
    }

}
