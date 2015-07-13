<?php

class actionUsersTabsEdit extends cmsAction {

    public function run($tab_id){

        if (!$tab_id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $form = $this->getForm('tab', array('edit'));

        $is_submitted = $this->request->has('submit');

        $tab = $users_model->getUsersProfilesTab($tab_id);

        if ($is_submitted){

            $tab = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $tab);

            if (!$errors){

                $users_model->updateUsersProfilesTab($tab_id, $tab);

                $this->redirectToAction('tabs');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/tab', array(
            'do' => 'edit',
            'tab' => $tab,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}

