<?php

class actionAdminUsersEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        $user = $this->model_users->getUser($id);

        if (!$user) {
            return cmsCore::error404();
        }

        $old_email = $user['email'];

        $form = $this->getForm('user', ['edit']);

        if ($this->request->has('submit')) {

            cmsCore::loadControllerLanguage('auth');

            $data = $form->parse($this->request, true);

            if (!$data['is_locked']) {
                $data['lock_until']  = null;
                $data['lock_reason'] = null;
            }

            $errors = $form->validate($this, $data);

            if (!$errors) {
				
				list($user, $data, $form) = cmsEventsManager::hook('users_before_edit_password', [$user, $data, $form]);

                if ($data['email'] && $old_email != $data['email']) {

                    cmsUser::setUPS('users.change_email_' . md5($data['email']), [
                        'accepted'  => 1,
                        'email'     => $old_email,
                        'timestamp' => time(),
                        'hash'      => string_random()
                    ]);

                    cmsUser::setUPS('users.change_email_' . md5($old_email), [
                        'accepted'  => 1,
                        'email'     => $data['email'],
                        'timestamp' => time(),
                        'hash'      => string_random()
                    ]);
                }

                $result = $this->model_users->updateUser($id, $data);

                if ($result['success']) {

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
					
					list($user, $data, $form) = cmsEventsManager::hook('users_after_edit_password', [$user, $data, $form]);

                    $back_url = $this->getRequestBackUrl();

                    if ($back_url) {
                        $this->redirect($back_url);
                    } else {
                        $this->redirectToAction('users');
                    }

                } else {
                    $errors = $result['errors'];
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('user', [
            'do'     => 'edit',
            'user'   => $user,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
