<?php

class actionAdminUsersAdd extends cmsAction {

    public function run($group_id = false) {

        $users_model = cmsCore::getModel('users');

        $form = $this->getForm('user', ['add']);

        $is_submitted = $this->request->has('submit');

        $user = $form->parse($this->request, $is_submitted);

        if (!$is_submitted) {
            $user['groups'] = [$group_id];
        }

        if ($is_submitted) {

            $errors = $form->validate($this, $user);

            if (mb_strlen($user['password1']) < 6) {
                $errors['password1'] = sprintf(ERR_VALIDATE_MIN_LENGTH, 6);
            }

            if (!$errors) {

                $result = $users_model->addUser($user);

                if ($result['success']) {

                    $user['id'] = $result['id'];

                    $user = cmsEventsManager::hook('user_registered', $user, null, $this->request);

                    cmsUser::addSessionMessage(sprintf(LANG_CP_USER_CREATED, $user['nickname']), 'success');

                    return $this->redirectToAction('users');

                } else {
                    $errors = $result['errors'];
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('user', [
            'do'     => 'add',
            'user'   => $user,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
