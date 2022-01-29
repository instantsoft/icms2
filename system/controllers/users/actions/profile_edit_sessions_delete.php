<?php

class actionUsersProfileEditSessionsDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $id = null) {

        if (!$id) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        $ses = $this->model->getItemById('{users}_auth_tokens', $id);
        if (!$ses) {
            return cmsCore::error404();
        }

        $this->model->deleteAuthToken($ses['auth_token']);

        cmsUser::addSessionMessage(LANG_USERS_SESSIONS_DELETE, 'success');

        $this->redirectToAction($profile['id'], ['edit', 'sessions']);
    }

}
