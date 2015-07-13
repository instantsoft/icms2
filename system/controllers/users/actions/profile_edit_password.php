<?php

class actionUsersProfileEditPassword extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        // проверяем наличие доступа
        if ($profile['id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $form = $this->getForm('password');

        $is_submitted = $this->request->has('submit');

        $data = array();

        if ($is_submitted){

            cmsCore::loadControllerLanguage('auth');

            $data = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this,  $data);

            if (!$errors){

                $password_hash = md5(md5($data['password']) . $user->password_salt);

                if ($password_hash != $user->password){
                    $errors = array('password' => LANG_OLD_PASS_INCORRECT);
                }

            }

            if (!$errors){

                $profile = array_merge($profile, $data);

                $result = $this->model->updateUser($profile['id'], $profile);

                if ($result['success']){
                    cmsUser::addSessionMessage(LANG_PASS_CHANGED, 'success');
                    $this->redirectTo('users', $profile['id']);
                } else {
                    $errors = $result['errors'];
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $template->render('profile_edit_password', array(
            'id' => $profile['id'],
            'profile' => $profile,
            'data' => $data,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
