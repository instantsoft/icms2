<?php

class actionUsersProfileRestore extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        if(!cmsUser::isAllowed('users', 'delete', 'any')){
            cmsCore::error404();
        }

        if ($this->request->has('submit')){

            $csrf_token = $this->request->get('csrf_token', '');

            if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

            $this->model->restoreUser($profile['id']);

            cmsUser::addSessionMessage(LANG_USERS_RESTORE_SUCCESS, 'success');

            cmsEventsManager::hook('restore_user', $profile);

            $this->redirectToAction($profile['id']);

        }

        return $this->cms_template->render('action_confirm', array(
            'confirm' => array(
                'action' => href_to('users', $profile['id'], 'restore'),
                'title'  => LANG_USERS_RESTORE_PROFILE.'?'
            )
        ));

    }

}
