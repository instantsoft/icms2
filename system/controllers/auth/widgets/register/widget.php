<?php
class widgetAuthRegister extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        if (cmsUser::isLogged()){ return false; }

        $auth = cmsCore::getController('auth');

        if (!$auth->options['is_reg_enabled']){
            return false;
        }

        list($form, $fieldsets) = $auth->getRegistrationForm();

        if ($auth->options['reg_captcha']){
            $captcha_html = cmsEventsManager::hook('captcha_html');
        }

        return array(
            'form'         => $form,
            'captcha_html' => isset($captcha_html) ? $captcha_html : false
        );

    }

}
