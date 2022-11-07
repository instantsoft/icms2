<?php

class actionAuthRestore extends cmsAction {

    public function run() {

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {
            return $this->redirectToHome();
        }

        // если аккаунт не подтверждён и время не вышло
        // редиректим на верификацию
        $reg_email = cmsUser::getCookie('reg_email');
        if ($reg_email && $this->validate_email($reg_email) === true) {

            cmsUser::addSessionMessage(sprintf(LANG_REG_SUCCESS_NEED_VERIFY, $reg_email), 'info');

            return $this->redirectToAction('verify');
        }

        $form = $this->getForm('restore');

        $data = [];

        if ($this->request->has('submit')) {

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            list($errors, $data) = cmsEventsManager::hook('auth_restore_validation', [$errors, $data]);

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            if (!$errors) {

                $user = $this->model_users->getUserByEmail($data['email']);

                // проверки на существование юзера находятся в классе формы
                if ($user['pass_token']) {

                    if ((strtotime($user['date_token']) + (24 * 3600)) < time()) {
                        $this->model_users->clearUserPassToken($user['id']);
                    }
                }

                $pass_token = hash('sha256', string_random(32, $user['email']));

                $this->model_users->updateUserPassToken($user['id'], $pass_token);

                $to     = ['email' => $user['email'], 'name' => $user['nickname']];
                $letter = ['name' => 'reg_restore'];

                $this->controller_messages->sendEmail($to, $letter, [
                    'nickname'    => $user['nickname'],
                    'page_url'    => href_to_abs('auth', 'reset', $pass_token),
                    'valid_until' => html_date(date('d.m.Y H:i', time() + (24 * 3600)), true)
                ]);

                cmsUser::addSessionMessage(LANG_TOKEN_SENDED, 'success');
            }
        }

        return $this->cms_template->render('restore', [
            'data'   => $data,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
