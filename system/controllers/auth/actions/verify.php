<?php
/**
 * @property \modelUsers $model_users
 */
class actionAuthVerify extends cmsAction {

    public function run($pass_token = null) {

        if (!$this->options['is_reg_enabled']) {
            return cmsCore::error404();
        }

        if (empty($this->options['verify_email'])) {
            return cmsCore::error404();
        }

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {
            return $this->redirectToHome();
        }

        $reg_email = cmsUser::getCookie('reg_email');

        $reg_user = [];

        if ($reg_email && $this->validate_email($reg_email) === true) {

            $reg_user = $this->model_users->filterNotNull('pass_token')->
                    filterEqual('is_locked', 1)->
                    getUserByEmail($reg_email);

            if ($reg_user) {

                $reg_user['resubmit_extime'] = modelAuth::RESUBMIT_TIME - (time() - strtotime($reg_user['date_token']));
                if ($reg_user['resubmit_extime'] < 0) {
                    $reg_user['resubmit_extime'] = 0;
                }
            }

        } else {

            cmsUser::unsetCookie('reg_email');

            $reg_email = false;
        }

        $form = $this->getForm('verify', [$reg_user]);

        $data = ['reg_token' => $pass_token];

        if ($this->request->has('submit')) {

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors) {

                $user = $this->model_users->getUserByPassToken($data['reg_token']);

                if (!$user) {
                    $errors['reg_token'] = LANG_VERIFY_EMAIL_ERROR;
                }
            }

            if (!$errors) {

                cmsUser::unsetCookie('reg_email');

                $this->model_users->unlockUser($user['id'])->clearUserPassToken($user['id']);

                $user = cmsEventsManager::hook('user_registered', $user);

                cmsUser::addSessionMessage($this->options['reg_auto_auth'] ? LANG_REG_SUCCESS_VERIFIED_AND_AUTH : LANG_REG_SUCCESS_VERIFIED, 'success');

                // авторизуем пользователя автоматически
                if ($this->options['reg_auto_auth']) {

                    $user = cmsEventsManager::hook('user_login', $user);

                    cmsUser::setUserSession($user);

                    $this->model_users->updateUserIp($user['id']);

                    cmsEventsManager::hook('auth_login', $user['id']);
                }

                $this->sendGreetMsg($user);

                return $this->redirect($this->getAuthRedirectUrl($this->options['first_auth_redirect']));
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render([
            'reg_email' => $reg_email,
            'data'      => $data,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
