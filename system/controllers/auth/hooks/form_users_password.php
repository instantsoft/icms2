<?php

class onAuthFormUsersPassword extends cmsAction {

    public function run($data){

        if(empty($this->options['2fa'])){
            return $data;
        }

        $providers = cmsEventsManager::hookAll('auth_twofactor_list');

        $items = [];

        if (is_array($providers)){
            foreach($providers as $provider){
                foreach($provider['types'] as $name => $title){
                    if(in_array($name, $this->options['2fa'])){
                        $items[$name] = $title;
                    }
                }
            }
        }

        if(!$items){
            return $data;
        }

        list($form, $params) = $data;

        $form->addFieldset(LANG_REG_CFG_AUTH_2FA, 'twofa', array(
            'childs' => array(
                new fieldList('2fa', array(
                    'title' => LANG_REG_TWOFA_APP,
                    'items' => ['' => LANG_NO] + $items
                ))
            )
        ));

        list($form, $params) = cmsEventsManager::hook('form_users_password_2fa', array($form, $params));

        return [$form, $params];

    }

}
