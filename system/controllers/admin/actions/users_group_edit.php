<?php

class actionAdminUsersGroupEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $group = $users_model->getGroup($id);
        if (!$group) { cmsCore::error404(); }

        $form = $this->getForm('users_group', array('edit'));

        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            $group = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $group);

            if (!$errors) {

                $users_model->updateGroup($id, $group);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('users');

            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('users_group', array(
            'do'     => 'edit',
            'menu'   => $this->getUserGroupsMenu('view', $group['id']),
            'group'  => $group,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
