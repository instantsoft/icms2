<?php

class onLanguagesWidgetOptionsFullForm extends cmsAction {

    public function run($form){

        if(empty($this->options['sources']['widgets']['options_full_form'])){
            return $form;
        }

        $this->enableMultilanguageFormFields($form);

        return $form;
    }

}
