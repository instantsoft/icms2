<?php

class actionFormsEdit extends cmsAction {

    public function run($id) {

        $do = 'edit';

        $form_data = $this->model->getForm($id);
        if(!$form_data){ cmsCore::error404(); }

        $fields = $this->model->filterEqual('form_id', $form_data['id'])->getFormFields();

        $form = $this->getForm('form', [$do, $form_data, $fields]);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $form_data = array_merge($form_data, $form->parse($this->request, $is_submitted));

            $errors = $form->validate($this, $form_data);

            if (!$errors){

                $id = $this->model->updateForm($form_data['id'], $form_data);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/add', [
            'menu'      => $this->getFormMenu($do, $form_data['id']),
            'form_data' => $form_data,
            'do'        => $do,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);

    }

}
