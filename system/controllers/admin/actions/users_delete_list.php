<?php

class actionAdminUsersDeleteList extends cmsAction {

    public function run() {

        $items = $this->request->get('selected', []);

        if (!$items) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        foreach ($items as $user_id) {
            if ($user_id && is_numeric($user_id)) {

                $user = $this->model_users->getUser($user_id);

                if (!$user) {
                    continue;
                }

                // Случайно сам себя чтобы не удалил
                if ($user['id'] == $this->cms_user->id) {
                    continue;
                }

                $user = cmsEventsManager::hook('user_delete', $user);

                $this->model_users->deleteUser($user);
            }
        }

        cmsUser::addSessionMessage(LANG_CP_USER_DELETED_LIST, 'success');

        return $this->redirectToAction('users');
    }

}
