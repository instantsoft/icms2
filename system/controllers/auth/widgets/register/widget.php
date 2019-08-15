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

        return array(
            'form' => $form,
        );

    }

}
