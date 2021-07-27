<?php

class actionUsersProfileRestore extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile) {

        if (!$this->cms_user->is_logged) {
            cmsCore::error404();
        }

        if (!cmsUser::isAllowed('users', 'delete', 'any')) {
            cmsCore::error404();
        }

        if ($this->request->has('submit')) {

            $csrf_token = $this->request->get('csrf_token', '');

            if (!cmsForm::validateCSRFToken($csrf_token)) {
                cmsCore::error404();
            }

            $this->model->restoreUser($profile['id']);

            cmsUser::addSessionMessage(LANG_USERS_RESTORE_SUCCESS, 'success');

            cmsEventsManager::hook('restore_user', $profile);

            $this->redirectToAction($profile['id']);
        }

        return $this->cms_template->renderAsset('ui/confirm', [
            'confirm_title'  => LANG_USERS_RESTORE_PROFILE . '?',
            'confirm_action' => href_to_profile($profile, 'restore')
        ], $this->request);
    }

}
