<?php
class actionAuthReset extends cmsAction {

    public function run($pass_token){

        if (!$pass_token) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $profile = $users_model->getUserByPassToken($pass_token);

        if (!$profile) { cmsCore::error404(); }

        $form = $this->getForm('reset');

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $profile = array_merge($profile, $form->parse($this->request, $is_submitted));

            $errors = $form->validate($this,  $profile);

            if (!$errors){

                $result = $users_model->updateUser($profile['id'], $profile);

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

        return cmsTemplate::getInstance()->render('reset', array(
            'profile' => $profile,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
