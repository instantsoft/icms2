<?php
class actionAuthLogin extends cmsAction {

    private $is_added_capcha_field = false;

    public function run(){

        $is_site_offline = !cmsConfig::get('is_site_on');

        $back_url = $this->getRequestBackUrl();

        $ajax_page_redirect = false;

        $data = [];

        // Авторизованных редиректим сразу
        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {

            if ($back_url){
                $this->redirect($back_url);
            } else {
                $this->redirect(href_to_profile($this->cms_user));
            }

        }

        $form = $this->getForm('login');

        if ($this->options['auth_captcha'] && cmsUser::sessionGet('is_auth_captcha')){
            $form = $this->addCapchaField($form);
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

                    $form = $this->addCapchaField($form);
                }

            } else {

                $logged_user = cmsUser::login($data['login_email'], $data['login_password'], $data['remember'], false);

                if ($logged_user){

                    cmsUser::sessionUnset('is_auth_captcha');

                    // Включена ли двухфакторная авторизация
                    if(!empty($logged_user['2fa']) && !empty($this->options['2fa_params'][$logged_user['2fa']])){

                        $twofa_params = $this->options['2fa_params'][$logged_user['2fa']];

                        $context_request = clone $this->request;

                        // Чтобы сработало свойство $lock_explicit_call в экшене $twofa_params['action']
                        $context_request->setContext(cmsRequest::CTX_INTERNAL);

                        $result = cmsCore::getController($twofa_params['controller'], $context_request)->
                                executeAction($twofa_params['action'], [$logged_user, $form, $data, href_to('auth', 'login')]);

                        // передаём управление другому экшену
                        if($result !== true){

                            $this->cms_template->addOutput($result);

                            return $result;
                        }

                        $this->cms_template->restoreContext();

                    }

                    // Не даём авторизоваться
                    // если сайт выключен и доступа к просмотру нет
                    if ($is_site_offline){
                        if (empty($logged_user['permissions']['auth']['view_closed']) && empty($logged_user['is_admin'])){

                            cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
                            $this->redirectBack();

                        }
                    }

                    // завершаем авторизацию
                    cmsUser::loginComplete($logged_user, $data['remember']);

                    // Переходное сообщение для нового типа хранения паролей
                    if(!empty($logged_user['is_old_auth']) && !empty($this->options['notify_old_auth'])){
                        cmsUser::addSessionMessage(sprintf(LANG_AUTH_IS_OLD_AUTH, href_to_profile($logged_user, ['edit', 'password'])), 'info');
                    }

                    cmsEventsManager::hook('auth_login', $logged_user['id']);

                    $auth_redirect = $this->options['auth_redirect'];

                    $is_first_auth = cmsUser::getUPS('first_auth', $logged_user['id']);

                    if ($is_first_auth){
                        $auth_redirect = $this->options['first_auth_redirect'];
                        cmsUser::deleteUPS('first_auth', $logged_user['id']);
                    }

                    if ($back_url){
                        $this->redirect($back_url);
                    } else {
                        $this->redirect($this->getAuthRedirectUrl($auth_redirect));
                    }

                } else {

                    cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');

                    if ($this->options['auth_captcha'] && !$is_site_offline){

                        cmsUser::sessionSet('is_auth_captcha', true);

                        $form = $this->addCapchaField($form);
                    }

                }

            }

            if ($is_site_offline) { $this->redirectBack(); }

        }

        if ($back_url && !$is_submit && empty($this->options['is_site_only_auth_users'])){
            cmsUser::addSessionMessage(LANG_LOGIN_REQUIRED, 'error');
        }

        if ($this->request->isAjax() && cmsUser::sessionGet('is_auth_captcha')){
            $ajax_page_redirect = true;
        }

        // Мы не передаём название шаблона для вывода
        // Оно берется из названия текущего экшена
        return $this->cms_template->render([
            'is_reg_enabled' => $this->options['is_reg_enabled'],
            'ajax_page_redirect' => $ajax_page_redirect,
            'errors'     => (isset($errors) ? $errors : false),
            'data'       => $data,
            'form'       => $form,
            'back_url'   => $back_url,
            'hooks_html' => cmsEventsManager::hookAll('login_form_html')
        ]);
    }

    private function addCapchaField($form) {

        if($this->is_added_capcha_field){
            return $form;
        }

        $fieldset_id = $form->addFieldset(LANG_CAPTCHA_CODE, 'regcaptcha');

        $form->addField($fieldset_id,
            new fieldCaptcha('capcha')
        );

        $this->is_added_capcha_field = true;

        return $form;
    }

}
