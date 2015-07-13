<?php

class backendRating extends cmsBackend{

    public $useDefaultOptionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
