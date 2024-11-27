<?php

class actionAdminUsersDelete extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $user = $this->model_users->getUser($id);

        if (!$user) {
            return cmsCore::error404();
        }

        // Случайно сам себя чтобы не удалил
        if ($user['id'] == $this->cms_user->id) {
            return $this->redirectToAction('users');
        }

        $user = cmsEventsManager::hook('user_delete', $user);

        $this->model_users->deleteUser($user);

        cmsUser::addSessionMessage(sprintf(LANG_CP_USER_DELETED, $user['nickname']), 'success');

        return $this->redirectToAction('users');
    }

}
