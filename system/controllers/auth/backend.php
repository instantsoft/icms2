<?php

class backendAuth extends cmsBackend{

    public $useDefaultOptionsAction = true;
    public $useSeoOptions = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
