<?php

class actionActivityOptions extends cmsAction {

    public function run(){

        $form = $this->getForm('options');
        if (!$form) { cmsCore::error404(); }

        $is_submitted = $this->request->has('submit');

        $options = cmsController::loadOptions($this->name);

        if ($is_submitted){

            $options = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $options);

            if (!$errors){

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                cmsController::saveOptions($this->name, $options);

                $this->model->enableTypes($options['types']);

                $this->redirectToAction('options');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/options', array(
            'options' => $options,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }


}