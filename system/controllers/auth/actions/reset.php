<?php
class actionAuthReset extends cmsAction {

    public function run($pass_token){

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) { $this->redirectToHome(); }

        if (!$pass_token) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $profile = $users_model->getUserByPassToken($pass_token);
        if (!$profile) { cmsCore::error404(); }

        if($profile['is_locked']){

            cmsUser::addSessionMessage(LANG_RESTORE_BLOCK.($profile['lock_reason'] ? '. '.$profile['lock_reason'] : ''), 'error');

            $this->redirectToHome();

        }

        if ((strtotime($profile['date_token']) + (24 * 3600)) < time()){

            $users_model->clearUserPassToken($profile['id']);

            cmsUser::addSessionMessage(LANG_RESTORE_TOKEN_EXPIRED, 'error');

            $this->redirectToAction('restore');

        }

        $form = $this->getForm('reset');

        if ($this->request->has('submit')){

            $_profile = $form->parse($this->request, true);

            $errors = $form->validate($this, $_profile);

            if (!$errors){

                $result = $users_model->updateUser($profile['id'], $_profile);

                if ($result['success']){

                    cmsUser::addSessionMessage(LANG_PASS_CHANGED, 'success');

                    $users_model->clearUserPassToken($profile['id']);

                    $this->redirectTo('users', $profile['id']);

                } else {
                    $errors = $result['errors'];
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('reset', array(
            'profile' => $profile,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
