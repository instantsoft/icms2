<?php

class actionAdminSettingsSchedulerAdd extends cmsAction {

    public function run(){

        $form = $this->getForm('scheduler_task', array('add'));

        $is_submitted = $this->request->has('submit');

        $task = array();

        if ($is_submitted){

            $task = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this,  $task);

            if (!$errors){

                $task_id = $this->model->addSchedulerTask($task);

                $this->redirectToAction('settings', array('scheduler'));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('settings_scheduler_task', array(
            'do' => 'add',
            'task' => $task,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
