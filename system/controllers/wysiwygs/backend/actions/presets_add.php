<?php

class actionWysiwygsPresetsAdd extends cmsAction {

    public function run($id = null, $is_copy = null){

        $preset = [];

        $do = 'add';

        if($id){

            $do = $is_copy ? 'add' : 'edit';

            $preset = $this->model->getPreset($id);

            if (!$preset) {
                return cmsCore::error404();
            }

            if($is_copy){
                $preset['title'] .= ' (1)';
            }

        }

        $form = $this->getForm('preset', array($do));

        if($do == 'edit'){

            $form->hideField('basic', 'wysiwyg_name');

            $this->cms_template->setPageH1(ucfirst($preset['wysiwyg_name']));

        }

        $errors = [];

        if ($this->request->has('submit')){

            $wysiwyg_name = $this->request->get('wysiwyg_name', '');

            if(!$wysiwyg_name){
                $errors['wysiwyg_name'] = ERR_VALIDATE_REQUIRED;
            }

            if (!$errors){

                $form_options = $this->getWysiwygOptionsForm($wysiwyg_name, [$do]);

                $structure = $form_options->getStructure();

                foreach($structure as $key => $fieldset){
                    $form->addFieldset($fieldset['title'], $key, $fieldset);
                }

                $preset = $form->parse($this->request, true);

                $errors = $form->validate($this,  $preset);

                if (!$errors){

                    if($do == 'add'){
                        $this->model->addPreset($preset);
                    } else {
                        $this->model->updatePreset($id, $preset);
                    }

                    cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                    $this->redirectToAction('presets');

                }

            }

            if ($errors){

                if($do == 'edit'){
                    $preset['id'] = $id;
                }

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/preset', array(
            'do'     => $do,
            'preset' => $preset,
            'form'   => $form,
            'errors' => $errors
        ));

    }

}
