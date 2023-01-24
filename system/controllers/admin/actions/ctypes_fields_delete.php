<?php
/**
 * @property \modelContent $model_content
 */
class actionAdminCtypesFieldsDelete extends cmsAction {

    public function run($ctype_id = null, $field_id = null) {

        if (!$ctype_id || !$field_id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model_content->deleteContentField($ctype_id, $field_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('ctypes', ['fields', $ctype_id]);
    }

}
