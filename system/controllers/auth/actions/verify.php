<?php
class actionAuthVerify extends cmsAction {

    public function run($pass_token = null){

        if (cmsUser::isLogged()) { $this->redirectToHome(); }

        $users_model = cmsCore::getModel('users');

        $form = $this->getForm('verify');

        $data = array('reg_token' => $pass_token);

        if ($this->request->has('submit')){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors){

                $user = $users_model->getUserByPassToken($data['reg_token']);

                if (!$user) {
                    $errors['reg_token'] = LANG_VERIFY_EMAIL_ERROR;
                }

            }

            if (!$errors){

                $users_model->unlockUser($user['id'])->clearUserPassToken($user['id']);

                cmsEventsManager::hook('user_registered', $user);

                cmsUser::addSessionMessage($this->options['reg_auto_auth'] ? LANG_REG_SUCCESS_VERIFIED_AND_AUTH : LANG_REG_SUCCESS_VERIFIED, 'success');

                // авторизуем пользователя автоматически
                if ($this->options['reg_auto_auth']){

                    $user = cmsEventsManager::hook('user_login', $user);

                    cmsUser::setUserSession($user);

                    $update_data = array(
                        'ip' => cmsUser::getIp()
                    );

                    $this->model->update('{users}', $user['id'], $update_data, true);

                    cmsEventsManager::hook('auth_login', $user['id']);

                }

                $this->redirect($this->getAuthRedirectUrl($this->options['first_auth_redirect']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('verify', array(
            'data'   => $data,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
