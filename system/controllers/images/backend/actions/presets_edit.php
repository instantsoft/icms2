<?php

class actionImagesPresetsEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $form = $this->getForm('preset', array('edit'));

        $is_submitted = $this->request->has('submit');

        $preset = $original_preset = $this->model->getPreset($id);

		if ($preset['is_internal']){
			$form->removeFieldset('basic');
		}

        if ($is_submitted){

            $preset = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $preset);

            if (!$errors){

                $this->model->updatePreset($id, $preset);

                $this->createDefaultImages(array_merge($original_preset, $preset));

                $this->redirectToAction('presets');

            }

            if ($errors){

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/preset', array(
            'do' => 'edit',
            'preset' => $preset,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}

