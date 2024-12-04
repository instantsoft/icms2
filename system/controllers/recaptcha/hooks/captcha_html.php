<?php

class onRecaptchaCaptchaHtml extends cmsAction {

    public function run() {

        if (empty($this->options['public_key'])) {
            return '';
        }

        return $this->cms_template->renderInternal($this, 'captcha');
    }

}
