<?php

class actionUsersProfileDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile) {

        if (!$this->cms_user->is_logged) {
            cmsCore::error404();
        }

        // проверяем наличие доступа
        $allow_delete_profile = (cmsUser::isAllowed('users', 'delete', 'any') ||
                (cmsUser::isAllowed('users', 'delete', 'my') && $this->is_own_profile));

        if (!$allow_delete_profile) {
            cmsCore::error404();
        }

        if ($this->request->has('submit')) {

            $csrf_token = $this->request->get('csrf_token', '');

            if (!cmsForm::validateCSRFToken($csrf_token)) {
                cmsCore::error404();
            }

            if ($this->is_own_profile) {

                cmsEventsManager::hook('auth_logout', $this->cms_user->id);

                cmsUser::logout();

            } else {

                if ($profile['is_admin']) {

                    cmsUser::addSessionMessage(LANG_USERS_DELETE_ADMIN_ERROR, 'error');

                    $this->redirectToHome();
                }
            }

            $this->model->setUserIsDeleted($profile['id']);

            cmsUser::addSessionMessage(LANG_USERS_DELETE_SUCCESS, 'success');

            cmsEventsManager::hook('set_user_is_deleted', $profile);

            $this->redirectToHome();
        }

        return $this->cms_template->renderAsset('ui/confirm', [
            'confirm_title'  => ($this->is_own_profile ? LANG_USERS_DELETE_PROFILE . '?' : sprintf(LANG_USERS_DELETE_CONFIRM, $profile['nickname'])),
            'confirm_action' => href_to_profile($profile, 'delete')
        ], $this->request);

    }

}
