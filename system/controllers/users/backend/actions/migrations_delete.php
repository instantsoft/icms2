<?php

class actionUsersMigrationsDelete extends cmsAction {

    public function run($rule_id) {

        if (!$rule_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $this->model->deleteMigrationRule($rule_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('migrations');

    }

}
