<?php

class actionUsersProfileEditSessionsDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $id=null){

        if(!$id){ cmsCore::error404(); }

        // проверяем наличие доступа
        if ($profile['id'] != $this->cms_user->id && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $ses = $this->model->getItemById('{users}_auth_tokens', $id);
        if(!$ses){ cmsCore::error404(); }

        $this->model->deleteAuthToken($ses['auth_token']);

        if ($ses['user_id'] == $this->cms_user->id) {
            self::unsetCookie('auth');
        }

        cmsUser::addSessionMessage(LANG_USERS_SESSIONS_DELETE, 'success');

        $this->redirectToAction($profile['id'], array('edit', 'sessions'));

    }

}
