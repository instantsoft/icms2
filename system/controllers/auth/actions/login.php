<?php
class actionAuthLogin extends cmsAction {

    public function run(){

        if (cmsUser::isLogged()) { $this->redirectToHome(); }

        $email      = $this->request->get('login_email');
        $password   = $this->request->get('login_password');
        $remember   = (bool)$this->request->get('remember');

        $back_url = $this->request->has('back') ?
                    $this->request->get('back') :
                    false;

        $is_site_offline = !cmsConfig::get('is_site_on');

        if ($this->request->has('submit')){

            $is_captcha_valid = true;

            if (cmsUser::sessionGet('is_auth_captcha') && $this->options['auth_captcha']){
                $is_captcha_valid = cmsEventsManager::hook('captcha_validate', $this->request);
            }

            if ($is_captcha_valid){

                cmsUser::sessionUnset('is_auth_captcha');

                $logged_id  = cmsUser::login($email, $password, $remember);

                if ( $logged_id ){

                    if ($is_site_offline){
						$userSession = cmsUser::sessionGet('user');
                        if (!$userSession['is_admin']){
                            cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
                            cmsUser::logout();
                            $this->redirectBack();
                        }
                    }

                    cmsEventsManager::hook('auth_login', $logged_id);

                    $is_back = $this->request->get('is_back');

                    if ($is_back){
                        $this->redirectBack();
                    }

                    if ($back_url){
                        $this->redirect($back_url);
                    } else {
                        $this->redirectToHome();
                    }

                }

            }

            if ($this->options['auth_captcha'] && !$is_site_offline){
                cmsUser::sessionSet('is_auth_captcha', true);
            }

            if ($is_captcha_valid){
                cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');
                if ($is_site_offline) { $this->redirectBack(); }
            } else {
                cmsUser::addSessionMessage(LANG_CAPTCHA_ERROR, 'error');
            }

        }

        if ($back_url){
            cmsUser::addSessionMessage(LANG_LOGIN_REQUIRED, 'error');
        }

        if (cmsUser::sessionGet('is_auth_captcha')){
            $captcha_html = cmsEventsManager::hook('captcha_html');
        }

        return cmsTemplate::getInstance()->render('login', array(
            'back_url' => $back_url,
            'captcha_html'=> isset($captcha_html) ? $captcha_html : false,
        ));

    }

}
