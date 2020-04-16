<?php

class onRecaptchaCaptchaHtml extends cmsAction {

    public function run(){

        return cmsTemplate::getInstance()->renderInternal($this, 'captcha');

    }

}
