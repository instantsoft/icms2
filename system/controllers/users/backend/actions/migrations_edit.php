<?php

class actionUsersMigrationsEdit extends cmsAction {

    public function run($rule_id){

        if (!$rule_id) { cmsCore::error404(); }

        $form = $this->getForm('migration', array('edit'));

        $is_submitted = $this->request->has('submit');

        $rule = $this->model->getMigrationRule($rule_id);

        if ($is_submitted){

            $rule = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $rule);

            if (!$errors){

                $this->model->updateMigrationRule($rule_id, $rule);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('migrations');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/migration', array(
            'do'     => 'edit',
            'rule'   => $rule,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
