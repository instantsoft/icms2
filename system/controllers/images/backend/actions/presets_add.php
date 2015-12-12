<?php

class actionImagesPresetsAdd extends cmsAction {

    public function run(){

        $form = $this->getForm('preset', array('add'));

        $is_submitted = $this->request->has('submit');

        $preset = array();

        if ($is_submitted){

            $preset = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this,  $preset);

            if (!$errors){

                $id = $this->model->addPreset($preset);

                // создаем дефолтные миниатюры
                $this->createDefaultImages($preset);

                $this->redirectToAction('presets');

            }

            if ($errors){

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/preset', array(
            'do' => 'add',
            'preset' => $preset,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}