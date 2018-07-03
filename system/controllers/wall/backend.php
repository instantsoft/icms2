<?php

class backendWall extends cmsBackend {

    public $useDefaultOptionsAction = true;
    protected $useOptions = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
