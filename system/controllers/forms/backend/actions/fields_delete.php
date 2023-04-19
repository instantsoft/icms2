<?php

class actionFormsFieldsDelete extends cmsAction {

    public function run($field_id) {

        $field = $this->model->getFormField($field_id);

        if (!$field) {
            return cmsCore::error404();
        }

        $form_data = $this->model->getForm($field['form_id']);

        if (!$form_data) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->deleteFormField($field_id, $field['form_id']);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('form_fields', [$form_data['id']]);
    }

}
