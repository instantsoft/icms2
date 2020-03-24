<?php

class actionUsersFieldsDelete extends cmsAction {

    public function run($field_id) {

        if (!$field_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $content_model->setTablePrefix('');

        $content_model->deleteContentField('{users}', $field_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('fields');

    }

}
