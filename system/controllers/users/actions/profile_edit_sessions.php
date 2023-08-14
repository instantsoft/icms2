<?php

class actionUsersProfileEditSessions extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile) {

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        if ($this->cms_user->is_admin && !$this->is_own_profile && $profile['is_admin']) {
            return cmsCore::error404();
        }

        return $this->cms_template->render('profile_edit_sessions', [
            'id'       => $profile['id'],
            'profile'  => $profile,
            'sessions' => $this->model->getUserAuthTokens($profile['id'])
        ]);
    }

}
