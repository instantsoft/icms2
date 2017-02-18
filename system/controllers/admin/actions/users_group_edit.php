<?php

class actionAdminUsersGroupEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');
        $group = $users_model->getGroup($id);
        if (!$group) { cmsCore::error404(); }

        $form = $this->getForm('users_group', array('edit'));

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $group = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $group);

            if (!$errors){

                $users_model->updateGroup($id, $group);

                $this->redirectToAction('users');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $template = cmsTemplate::getInstance();

        $template->setMenuItems('users_group', array(
            array(
                'title' => LANG_CONFIG,
                'url' => href_to($this->name, 'users', array('group_edit', $id))
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url' => href_to($this->name, 'users', array('group_perms', $id))
            )
        ));

        return $template->render('users_group', array(
            'do' => 'edit',
            'group' => $group,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
