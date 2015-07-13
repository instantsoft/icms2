<?php

class actionAdminIndex extends cmsAction {

    public function run(){

        $this->redirectToAction('content');

        return cmsTemplate::getInstance()->render('index');

    }

}
