<?php

class actionAdminCtypesFieldsDelete extends cmsAction {

    public function run($ctype_id, $field_id){

        if (!$ctype_id || !$field_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $this->model_content->deleteContentField($ctype_id, $field_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('ctypes', array('fields', $ctype_id));

    }

}
