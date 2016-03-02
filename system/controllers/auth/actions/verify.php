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

        cmsUser::addSessionMessage($this->options['reg_auto_auth'] ? LANG_REG_SUCCESS_VERIFIED_AND_AUTH : LANG_REG_SUCCESS_VERIFIED, 'success');

		// авторизуем пользователя автоматически
		if ($this->options['reg_auto_auth']){

			$user = cmsEventsManager::hook('user_login', $user);

			cmsUser::sessionSet('user', array(
				'id' => $user['id'],
				'groups' => $user['groups'],
				'time_zone' => $user['time_zone'],
				'perms' => cmsUser::getPermissions($user['groups'], $user['id']),
				'is_admin' => $user['is_admin'],
			));

			$update_data = array(
				'ip' => cmsUser::getIp()
			);

			$this->model->update('{users}', $user['id'], $update_data, true);

			if ( $user['id'] ){

				if (!cmsConfig::get('is_site_on')){
					$userSession = cmsUser::sessionGet('user');
					if (!$userSession['is_admin']){
						cmsUser::addSessionMessage(LANG_LOGIN_ADMIN_ONLY, 'error');
						cmsUser::logout();
						$this->redirectBack();
					}
				}

				cmsEventsManager::hook('auth_login', $user['id']);

			}
		}

		if ($this->options['first_auth_redirect']){
			
		}
		
        $this->redirect($this->authRedirectUrl($this->options['first_auth_redirect']));

    }

}
