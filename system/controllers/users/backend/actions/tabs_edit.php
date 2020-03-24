<?php

class actionUsersTabsEdit extends cmsAction {

    public function run($tab_id){

        if (!$tab_id) { cmsCore::error404(); }

        $form = $this->getForm('tab', array('edit'));

        $is_submitted = $this->request->has('submit');

        $tab = $this->model->getUsersProfilesTab($tab_id);

        if ($is_submitted){

            $tab = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $tab);

            if (!$errors){

                $this->model->updateUsersProfilesTab($tab_id, $tab);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('tabs');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/tab', array(
            'do'     => 'edit',
            'tab'    => $tab,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
