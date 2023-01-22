<?php

class onLanguagesFormMake extends cmsAction {

    public function run($form){

        $this->enableMultilanguageFormFields($form);

        return $form;
    }

}
