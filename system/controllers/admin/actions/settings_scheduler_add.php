<?php

class actionAdminSettingsSchedulerAdd extends cmsAction {

    public function run() {

        $form = $this->getForm('scheduler_task', ['add']);

        $task = [];

        if ($this->request->has('submit')) {

            $task = $form->parse($this->request, true);

            $errors = $form->validate($this, $task);

            if (!$errors) {

                $this->model->addSchedulerTask($task);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                return $this->redirectToAction('settings', ['scheduler']);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('settings_scheduler_task', [
            'do'     => 'add',
            'task'   => $task,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
