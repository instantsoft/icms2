<?php

class fieldCaptcha extends cmsFormField {

    public $title     = LANG_CAPTCHA_CODE;
    public $is_public = true;

    public $excluded_controllers = ['content', 'users', 'groups'];

    public function getOptions() {
        return [
            new fieldList('captcha_controller', [
                'title' => LANG_CAPTCHA_TYPE,
                'rules' => [
                    ['required']
                ],
                'generator' => function(){

                    $items = ['' => ''];

                    $captcha_list = cmsEventsManager::hookAll('captcha_list');

                    if (is_array($captcha_list)) {
                        foreach ($captcha_list as $name => $title) {
                            $items[$name] = $title;
                        }
                    }

                    return $items;
                }
            ])
        ];
    }

    public function getRules() {

        $this->rules[] = ['captcha'];

        return $this->rules;
    }

    public function parse($value) {
        return '';
    }

    public function getInput($value) {

        $this->data['captcha_html'] = cmsEventsManager::runHook($this->getOption('captcha_controller', 'recaptcha'), 'captcha_html');

        return parent::getInput($value);
    }

    public function validate_captcha($value) {

        $is_captcha_valid = cmsEventsManager::runHook($this->getOption('captcha_controller', 'recaptcha'), 'captcha_validate', $this->request);

        return $is_captcha_valid ? true : LANG_CAPTCHA_ERROR;
    }

}
