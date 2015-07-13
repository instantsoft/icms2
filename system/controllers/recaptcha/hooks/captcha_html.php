<?php

class onRecaptchaCaptchaHtml extends cmsAction {

    public function run(){

        $this->includeRecaptchaLib();
        
        $template = cmsTemplate::getInstance();

        return $template->renderInternal($this, 'captcha', array(
            'theme' => $this->options['theme'],
            'lang' => $this->options['lang'],
            'public_key' => $this->options['public_key'],
        ));

    }

}
