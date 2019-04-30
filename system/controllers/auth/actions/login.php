<?php
class actionAuthLogin extends cmsAction {

    public function run(){

        $email    = $this->request->get('login_email', '');
        $password = $this->request->get('login_password', '');
        $remember = (bool)$this->request->get('remember');
        $back_url = $this->request->get('back', '');

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {

            if ($back_url){
                $this->redirect($back_url);
            } else {
                $this->redirect(href_to_profile($this->cms_user));
            }

        }

        $is_site_offline = !cmsConfig::get('is_site_on');
        $is_submit = $this->request->has('submit');

        if ($is_submit){

            if (!$password || !$email || $this->validate_email($email) !== true){

                cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');
                $this->redirectBack();

            }

            $is_captcha_valid = true;

            if (cmsUser::sessionGet('is_auth_captcha') && $this->options['auth_captcha']){
                $is_captcha_valid = cmsEventsManager::hook('captcha_validate', $this->request);
            }

            if ($is_captcha_valid){

                cmsUser::sessionUnset('is_auth_captcha');

                $logged_id  = cmsUser::login($email, $password, $remember);

                if ($logged_id){

                    $userSession = cmsUser::sessionGet('user');

                    if ($is_site_offline){
                        if (empty($userSession['perms']['auth']['view_closed']) && empty($userSession['is_admin'])){
                            cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
                            cmsUser::logout();
                            $this->redirectBack();
                        }
                    }

                    if(!empty($userSession['is_old_auth']) && !empty($this->options['notify_old_auth'])){
                        cmsUser::addSessionMessage(sprintf(LANG_AUTH_IS_OLD_AUTH, href_to('users', $logged_id, ['edit', 'password'])), 'info');
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
            'hooks_html'   => cmsEventsManager::hookAll('login_form_html'),
            'captcha_html' => (isset($captcha_html) ? $captcha_html : false)
        ));

    }

}
