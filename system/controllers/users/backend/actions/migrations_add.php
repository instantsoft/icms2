<?php

class actionUsersMigrationsAdd extends cmsAction {

    public function run() {

        $form = $this->getForm('migration', array('add'));

        $rule = [];

        if ($this->request->has('submit')) {

            $rule = $form->parse($this->request, true);

            $errors = $form->validate($this, $rule);

            if (!$errors) {

                $rule_id = $this->model->addMigrationRule($rule);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('migrations');

            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/migration', array(
            'do'     => 'add',
            'rule'   => $rule,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
