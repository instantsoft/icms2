<?php

class backendSitemap extends cmsBackend{

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
