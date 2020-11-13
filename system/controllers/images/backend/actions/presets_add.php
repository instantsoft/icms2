<?php

class actionImagesPresetsAdd extends cmsAction {

    public function run(){

        $form = $this->getForm('preset', array('add'));

        $preset = array();

        if ($this->request->has('submit')){

            $preset = $form->parse($this->request, true);

            $errors = $form->validate($this,  $preset);

            if (!$errors){

                if((!$preset['width'] && !$preset['height']) ||
                        ($preset['is_square'] && (!$preset['width'] || !$preset['height']))){

                    if(!$preset['width']){
                        $errors['width'] = ERR_VALIDATE_REQUIRED;
                    }
                    if(!$preset['height']){
                        $errors['height'] = ERR_VALIDATE_REQUIRED;
                    }

                }

            }

            if (!$errors){

                $id = $this->model->addPreset($preset);

                $preset = cmsEventsManager::hook('images_preset_after_add', $preset);

                // создаем дефолтные миниатюры
                $this->createDefaultImages($preset);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('presets');

            }

            if ($errors){

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return $this->cms_template->render('backend/preset', array(
            'do'     => 'add',
            'preset' => $preset,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
