<?php

class actionImagesPresetsEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $form = $this->getForm('preset', array('edit'));

        $preset = $original_preset = $this->model->getPreset($id);

		if ($preset['is_internal']){
			$form->removeFieldset('basic');
		}

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

                $this->model->updatePreset($id, $preset);

                $preset = cmsEventsManager::hook('images_preset_after_update', $preset);

                $this->createDefaultImages(array_merge($original_preset, $preset));

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('presets');

            }

            if ($errors){

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return $this->cms_template->render('backend/preset', array(
            'do'     => 'edit',
            'preset' => $preset,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}

