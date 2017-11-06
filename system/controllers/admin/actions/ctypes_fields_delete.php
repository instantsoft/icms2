<?php

class actionAdminCtypesFieldsDelete extends cmsAction {

    public function run($ctype_id, $field_id){

        if (!$ctype_id || !$field_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $content_model->deleteContentField($ctype_id, $field_id);

        $this->redirectToAction('ctypes', array('fields', $ctype_id));

    }

}
