<?php

class actionAdminCtypesFiltersDelete extends cmsAction {

    public function run($ctype_id, $id){

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $this->model_content->deleteContentFilter($ctype, $id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

}
