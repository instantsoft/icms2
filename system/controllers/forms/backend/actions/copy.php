<?php

class actionFormsCopy extends cmsAction {

    public function run($form_id = 0) {

        $form_data = $this->model->localizedOff()->getForm($form_id);

        if (!$form_data) {
            return cmsCore::error404();
        }

        $this->model->localizedRestore();

        $do = 'copy';

        $form = $this->getForm('form', [$do]);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            $form_data = array_merge($form_data, $form->parse($this->request, $is_submitted));

            $errors = $form->validate($this, $form_data);

            if (!$errors) {

                unset($form_data['id']);

                $id = $this->model->addForm($form_data);

                $fields = $this->model->filterEqual('form_id', $form_id)->orderBy('ordering', 'asc')->get('forms_fields');

                if ($fields) {
                    foreach ($fields as $field) {

                        unset($field['id']);

                        $field['form_id'] = $id;

                        $this->model->addFormField($field);
                    }
                }

                cmsUser::addSessionMessage(sprintf(LANG_FORMS_CP_FORMS_COPY_CREATED, $form_data['title']), 'success');

                return $this->redirectToAction('form_fields', [$id]);
            }

            if ($errors) {
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
