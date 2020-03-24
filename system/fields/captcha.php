<?php

class fieldCaptcha extends cmsFormField {

    public $title = LANG_CAPTCHA_CODE;
    public $is_public = false;

    public function getRules() {

        $this->rules[] = array('captcha');

        return $this->rules;

    }

    public function getInput($value) {

        $this->data['captcha_html'] = cmsEventsManager::hook('captcha_html');

        return parent::getInput($value);

    }

    public function validate_captcha($value) {

        $is_captcha_valid = cmsEventsManager::hook('captcha_validate', $this->request);

        return $is_captcha_valid ? true : LANG_CAPTCHA_ERROR;

    }

}
