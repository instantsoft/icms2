<?php

class actionAdminUsersEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');
        $user = $users_model->getUser($id);
        if (!$user) { cmsCore::error404(); }

        $form = $this->getForm('user', array('edit'));

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            cmsCore::loadControllerLanguage('auth');

            $user = $form->parse($this->request, $is_submitted);

            if (!$user['is_locked']){
                $user['lock_until'] = null;
                $user['lock_reason'] = null;
            }

            $errors = $form->validate($this,  $user);

            if (!$errors){

                $result = $users_model->updateUser($id, $user);

                if ($result['success']){

                    $back_url = $this->request->get('back');

                    if ($back_url){
                        $this->redirect($back_url);
                    } else {
                        $this->redirectToAction('users');
                    }

                } else {
                    $errors = $result['errors'];
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('user', array(
            'do' => 'edit',
            'user' => $user,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
