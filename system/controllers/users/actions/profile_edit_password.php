<?php

class actionUsersProfileEditPassword extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $form = $this->getForm('password', [$profile]);

        $data = array(
            '2fa' => $profile['2fa']
        );

        if ($this->request->has('submit')){

            cmsCore::loadControllerLanguage('auth');

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors){

                list($profile, $data, $form) = cmsEventsManager::hook('users_before_edit_password', [$profile, $data, $form]);

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

        return $this->cms_template->render('profile_edit_password', array(
            'id'      => $profile['id'],
            'profile' => $profile,
            'data'    => $data,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
