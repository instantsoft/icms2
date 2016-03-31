<?php
class actionAuthLogin extends cmsAction {

    public function run(){

        if (cmsUser::isLogged()) { $this->redirectToHome(); }

        $email    = $this->request->get('login_email', '');
        $password = $this->request->get('login_password', '');
        $remember = (bool)$this->request->get('remember');
        $back_url = $this->request->get('back', '');

        $is_site_offline = !cmsConfig::get('is_site_on');
        $is_submit = $this->request->has('submit');

        if ($is_submit){

            $is_captcha_valid = true;

            if (cmsUser::sessionGet('is_auth_captcha') && $this->options['auth_captcha']){
                $is_captcha_valid = cmsEventsManager::hook('captcha_validate', $this->request);
            }

            if ($is_captcha_valid){

                cmsUser::sessionUnset('is_auth_captcha');

                $logged_id  = cmsUser::login($email, $password, $remember);

                if ($logged_id){

                    if ($is_site_offline){
						$userSession = cmsUser::sessionGet('user');
                        if (!$userSession['is_admin']){
                            cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
                            cmsUser::logout();
                            $this->redirectBack();
                        }
                    }

                    cmsEventsManager::hook('auth_login', $logged_id);

                    $auth_redirect = $this->options['auth_redirect'];

                    $is_first_auth = cmsUser::getUPS('first_auth', $logged_id);

                    if ($is_first_auth){
                        $auth_redirect = $this->options['first_auth_redirect'];
                        cmsUser::deleteUPS('first_auth', $logged_id);
                    }

                    if ($back_url){
                        $this->redirect($back_url);
                    } else {
                        $this->redirect($this->getAuthRedirectUrl($auth_redirect));
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

            if($this->options['auth_redirect'] == 'none' || (!empty($is_first_auth) && $this->options['first_auth_redirect'] == 'none')){

                if(!$back_url){ $back_url = $this->getBackURL(); }

            }

        }

        if ($back_url && !$is_submit){
            cmsUser::addSessionMessage(LANG_LOGIN_REQUIRED, 'error');
        }

        if (cmsUser::sessionGet('is_auth_captcha')){
            $captcha_html = cmsEventsManager::hook('captcha_html');
        }

        return $this->cms_template->render('login', array(
            'back_url'     => $back_url,
            'captcha_html' => (isset($captcha_html) ? $captcha_html : false)
        ));

    }

}
