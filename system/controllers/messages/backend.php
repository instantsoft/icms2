<?php

class backendMessages extends cmsBackend{

    public $useDefaultOptionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
