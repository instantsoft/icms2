<?php

class actionAdminUsersGroupAdd extends cmsAction {

    public function run(){

        $users_model = cmsCore::getModel('users');
        $form = $this->getForm('users_group', array('add'));

        $is_submitted = $this->request->has('submit');

        $group = $form->parse($this->request, $is_submitted);

        if ($is_submitted){

            $errors = $form->validate($this,  $group);

            if (!$errors){

                $id = $users_model->addGroup($group);

                cmsUser::addSessionMessage(sprintf(LANG_CP_USER_GROUP_CREATED, $group['title']), 'success');

                $this->redirectToAction('users', array('group_perms', $id));

            }

            if ($errors){
            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $template = cmsTemplate::getInstance();

        $template->setMenuItems('users_group', array(
            array(
                'title' => LANG_CONFIG,
                'url' => href_to($this->name, 'users', 'group_add')
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'disabled' => true
            )
        ));

        return $template->render('users_group', array(
            'do' => 'add',
            'group' => $group,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
