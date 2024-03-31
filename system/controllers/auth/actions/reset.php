<?php
/**
 * @property \modelUsers $model_users
 */
class actionAuthReset extends cmsAction {

    public function run($pass_token) {

        if (!empty($this->options['disable_restore'])) {
            return cmsCore::error404();
        }

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {
            return$this->redirectToHome();
        }

        if (!$pass_token) {
            return cmsCore::error404();
        }

        $profile = $this->model_users->getUserByPassToken($pass_token);
        if (!$profile) {
            return cmsCore::error404();
        }

        if ($profile['is_locked']) {

            cmsUser::addSessionMessage(LANG_RESTORE_BLOCK . ($profile['lock_reason'] ? '. ' . $profile['lock_reason'] : ''), 'error');

            return $this->redirectToHome();
        }

        if ((strtotime($profile['date_token']) + (24 * 3600)) < time()) {

            $this->model_users->clearUserPassToken($profile['id']);

            cmsUser::addSessionMessage(LANG_RESTORE_TOKEN_EXPIRED, 'error');

            return $this->redirectToAction('restore');
        }

        $form = $this->getForm('reset');

        if ($this->request->has('submit')) {

            $_profile = $form->parse($this->request, true);

            $errors = $form->validate($this, $_profile);

            if (!$errors) {

                $result = $this->model_users->updateUser($profile['id'], $_profile);

                if ($result['success']) {

                    cmsUser::addSessionMessage(LANG_PASS_CHANGED, 'success');

                    $this->model_users->clearUserPassToken($profile['id']);

                    return $this->redirectTo('users', $profile['id']);

                } else {

                    $errors = $result['errors'];
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('reset', [
            'profile' => $profile,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ]);
    }

}
