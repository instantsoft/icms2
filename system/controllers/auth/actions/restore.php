<?php
class actionAuthRestore extends cmsAction {

    public function run(){

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) { $this->redirectToHome(); }

        // если аккаунт не подтверждён и время не вышло
        // редиректим на верификацию
        $reg_email = cmsUser::getCookie('reg_email');
        if($reg_email && $this->validate_email($reg_email) === true){

            cmsUser::addSessionMessage(sprintf(LANG_REG_SUCCESS_NEED_VERIFY, $reg_email), 'info');

            $this->redirectToAction('verify');

        }

        $users_model = cmsCore::getModel('users');

        $form = $this->getForm('restore');

        $data = array();

        if ($this->request->has('submit')){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this,  $data);

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            if (!$errors){

                $user = $users_model->getUserByEmail($data['email']);

                if (!$user){

                    $errors['email'] = LANG_EMAIL_NOT_FOUND;

                } elseif($user['is_locked']) {

                    $errors['email'] = LANG_RESTORE_BLOCK.($user['lock_reason'] ? '. '.$user['lock_reason'] : '');

                } elseif($user['pass_token']) {

                    if ((strtotime($user['date_token']) + (24 * 3600)) < time()){
                        $users_model->clearUserPassToken($user['id']);
                    } else {
                        $errors['email'] = LANG_RESTORE_TOKEN_IS_SEND;
                    }

                } else {

                    $pass_token = string_random(32, $user['email']);

                    $users_model->updateUserPassToken($user['id'], $pass_token);

                    $messenger = cmsCore::getController('messages');
                    $to = array('email' => $user['email'], 'name' => $user['nickname']);
                    $letter = array('name' => 'reg_restore');

                    $messenger->sendEmail($to, $letter, array(
                        'nickname' => $user['nickname'],
                        'page_url' => href_to_abs('auth', 'reset', $pass_token),
                        'valid_until' => html_date(date('d.m.Y H:i', time() + (24 * 3600)), true),
                    ));

                    cmsUser::addSessionMessage(LANG_TOKEN_SENDED, 'success');

                }

            }

        }

        return $this->cms_template->render('restore', array(
            'data'   => $data,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
