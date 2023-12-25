<?php

class onAdminAdminConfirmLogin extends cmsAction {

    public function run($data){

        if(empty($this->cms_config->check_spoofing_type)){
            return $data;
        }

        $data['allow'] = $this->cms_user->checkSpoofingSession($this->cms_config->check_spoofing_type == 2);

        if(!$data['allow']){

            $data['pagetitle'] = LANG_CONFIRM_LOGIN;
            $data['title']     = sprintf(LANG_CONFIRM_LOGIN_HINT, $this->cms_user->nickname);
            $data['hint']      = LANG_CONFIRM_LOGIN_HINT1;
            $data['form']      = $this->getForm('confirm_login');

            $this->request = $this->cms_core->request;

            if($this->request->has('submit')){

                $form_data = $data['form']->parse($this->request, true);

                $data['errors'] = $data['form']->validate($this, $form_data);

                if (!$data['errors']){

                    $user = $this->model_users->getUserByAuth($this->cms_user->email, $form_data['password']);

                    if($user){

                        cmsUser::sessionUnset('user_ip');

                        cmsUser::restrictSessionToIp();

                        $this->model_users->updateUserIp($user['id']);

                        $data['allow'] = true;

                        $this->request->set('submit', null);

                    } else {

                        $data['errors']['password'] = LANG_OLD_PASS_INCORRECT;

                    }

                }

                if ($data['errors']){
                    cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
                }

            }

            // если хотим использовать свой шаблон, то строку ниже раскомментировать
            // $this->cms_template->setContext($this);

        }

        return $data;
    }

}
