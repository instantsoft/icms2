<?php

class backendImages extends cmsBackend{

    public function actionIndex(){
        $this->redirectToAction('presets');
    }

}