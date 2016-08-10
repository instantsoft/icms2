<?php

class actionUsersMigrationsAdd extends cmsAction {

    public function run(){

        $form = $this->getForm('migration', array('add'));

        $is_submitted = $this->request->has('submit');

        $rule = array();

        if ($is_submitted){

            $rule = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this,  $rule);

            if (!$errors){

                $rule_id = $this->model->addMigrationRule($rule);

                $this->redirectToAction('migrations');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/migration', array(
            'do' => 'add',
            'rule' => $rule,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}