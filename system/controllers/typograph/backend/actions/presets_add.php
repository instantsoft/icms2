<?php
/**
 * @property \cmsModel $model
 */
class actionTypographPresetsAdd extends cmsAction {

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

        $form = $this->getForm('preset', [$do, $this->getHtmlTags()]);

        $errors = [];

        if ($this->request->has('submit')){

            $allowed_tags = $this->request->get('options:allowed_tags', []);

            $form_attr = $this->getTagsForm($allowed_tags);

            $structure = $form_attr->getStructure();

            foreach($structure as $key => $fieldset){
                $form->addFieldset($fieldset['title'], $key, $fieldset);
            }

            $preset = $form->parse($this->request, true);

            $errors = $form->validate($this, $preset);

            if (!$errors){

                if($do === 'add'){
                    $this->model->addPreset($preset);
                } else {
                    $this->model->updatePreset($id, $preset);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('');
            }

            if ($errors){

                if($do === 'edit'){
                    $preset['id'] = $id;
                }

				cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/preset', [
            'do'     => $do,
            'preset' => $preset,
            'form'   => $form,
            'errors' => $errors
        ]);
    }

}
