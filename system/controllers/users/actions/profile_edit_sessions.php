<?php

class actionUsersProfileEditSessions extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        return $this->cms_template->render('profile_edit_sessions', array(
            'id'       => $profile['id'],
            'profile'  => $profile,
            'sessions' => $this->model->getUserAuthTokens($profile['id'])
        ));

    }

}
