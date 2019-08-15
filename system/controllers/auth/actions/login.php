<?php
class actionAuthLogin extends cmsAction {

    public function run(){

        $is_site_offline = !cmsConfig::get('is_site_on');

        $back_url = $this->request->get('back', '');

        $ajax_page_redirect = false;

        $data = array();

        // Авторизованных редиректим сразу
        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {

            if ($back_url){
                $this->redirect($back_url);
            } else {
                $this->redirect(href_to_profile($this->cms_user));
            }

        }

        $form = $this->getForm('login');

        if (cmsUser::sessionGet('is_auth_captcha')){

            $fieldset_id = $form->addFieldset(LANG_CAPTCHA_CODE, 'regcaptcha');

            $form->addField($fieldset_id,
                new fieldCaptcha('capcha')
            );

        }

        if ($back_url){

            $form->addField('basic',
                new fieldHidden('back')
            );

            $data['back'] = $back_url;

        }

        $is_submit = $this->request->has('submit');

        if ($is_submit){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this,  $data);

            if ($errors){

                cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');

                if ($this->options['auth_captcha'] && !$is_site_offline){
                    cmsUser::sessionSet('is_auth_captcha', true);
                }

            }

            if (!$errors){

                $logged_id  = cmsUser::login($data['login_email'], $data['login_password'], $data['remember']);

                if ($logged_id){

                    cmsUser::sessionUnset('is_auth_captcha');

                    $userSession = cmsUser::sessionGet('user');

                    // Запрещаем и разавторизовываем пользователей,
                    // если сайт выключен и доступа к просмотру нет
                    if ($is_site_offline){
                        if (empty($userSession['perms']['auth']['view_closed']) && empty($userSession['is_admin'])){
                            cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
                            cmsUser::logout();
                            $this->redirectBack();
                        }
                    }

                    // Переходное сообщение для нового типа хранения паролей
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

            if ($is_site_offline) { $this->redirectBack(); }

        }

        if ($back_url && !$is_submit){
            cmsUser::addSessionMessage(LANG_LOGIN_REQUIRED, 'error');
        }

        if ($this->request->isAjax() && cmsUser::sessionGet('is_auth_captcha')){
            $ajax_page_redirect = true;
        }

        return $this->cms_template->render('login', array(
            'ajax_page_redirect' => $ajax_page_redirect,
            'errors'     => (isset($errors) ? $errors : false),
            'data'       => $data,
            'form'       => $form,
            'back_url'   => $back_url,
            'hooks_html' => cmsEventsManager::hookAll('login_form_html')
        ));

    }

}
