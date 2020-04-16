<?php

class actionAdminSettingsSchedulerEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) { cmsCore::error404(); }

        $form = $this->getForm('scheduler_task', array('edit'));

        $task = $this->model->getSchedulerTask($id);

        if ($this->request->has('submit')) {

            $task = $form->parse($this->request, true);
            $errors = $form->validate($this, $task);

            if (!$errors) {

                $this->model->updateSchedulerTask($id, $task);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('settings', array('scheduler'));
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('settings_scheduler_task', array(
            'do'     => 'edit',
            'task'   => $task,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
