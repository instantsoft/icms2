<?php

class actionImagesPresetsEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $images_model = cmsCore::getModel('images');

        $form = $this->getForm('preset', array('edit'));

        $is_submitted = $this->request->has('submit');

        $preset = $images_model->getPreset($id);

		if ($preset['is_internal']){
			$form->removeFieldset('basic');
		}

        if ($is_submitted){

            $preset = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $preset);

            if (!$errors){

                $images_model->updatePreset($id, $preset);

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

