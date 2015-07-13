<?php

class actionAdminSettingsSchedulerEdit extends cmsAction {

    public function run($id=false){

        if (!$id) { cmsCore::error404(); }

        $form = $this->getForm('scheduler_task', array('edit'));

        $is_submitted = $this->request->has('submit');

        $task = $this->model->getSchedulerTask($id);

        if ($is_submitted){

            $task = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $task);

            if (!$errors){

                $this->model->updateSchedulerTask($id, $task);

                $this->redirectToAction('settings', array('scheduler'));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('settings_scheduler_task', array(
            'do' => 'edit',
            'task' => $task,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
