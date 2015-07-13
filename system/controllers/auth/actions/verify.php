<?php
class actionAuthVerify extends cmsAction {

    public function run($pass_token){

        if (!$pass_token) { cmsCore::error404(); }

        if (cmsUser::isLogged()) { $this->redirectToHome(); }

        $users_model = cmsCore::getModel('users');

        $user = $users_model->getUserByPassToken($pass_token);

        if (!$user) { cmsCore::error404(); }

        $users_model->unlockUser($user['id']);
        $users_model->clearUserPassToken($user['id']);
		
		cmsEventsManager::hook('user_registered', $user);

        cmsUser::addSessionMessage(LANG_REG_SUCCESS_VERIFIED, 'success');

        $this->redirectToHome();

    }

}
