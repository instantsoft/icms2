<?php

class actionAdminUsersEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) { cmsCore::error404(); }

        $user = $this->model_users->getUser($id);

        if (!$user) {
            return cmsCore::error404();
        }

        $old_email = $user['email'];

        $form = $this->getForm('user', array('edit'));

        if ($this->request->has('submit')) {

            cmsCore::loadControllerLanguage('auth');

            $user = $form->parse($this->request, true);

            if (!$user['is_locked']) {
                $user['lock_until']  = null;
                $user['lock_reason'] = null;
            }

            $errors = $form->validate($this, $user);

            if (!$errors) {

                if($user['email'] && $old_email != $user['email']){

                    cmsUser::setUPS('users.change_email_'.md5($user['email']), [
                        'accepted'  => 1,
                        'email'     => $old_email,
                        'timestamp' => time(),
                        'hash'      => string_random()
                    ]);

                    cmsUser::setUPS('users.change_email_'.md5($old_email), [
                        'accepted'  => 1,
                        'email'     => $user['email'],
                        'timestamp' => time(),
                        'hash'      => string_random()
                    ]);

                }

                $result = $this->model_users->updateUser($id, $user);

                if ($result['success']) {

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                    $back_url = $this->request->get('back');

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

        return $this->cms_template->render('user', array(
            'do'     => 'edit',
            'user'   => $user,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
