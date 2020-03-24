<?php

class actionAdminUsersDelete extends cmsAction {

    public function run($id=false){

        if (!$id){ cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $users_model = cmsCore::getModel('users');

        $user = $users_model->getUser($id);

        $user = cmsEventsManager::hook('user_delete', $user);

        if ($user !== false) {

            $users_model->deleteUser($user);

            cmsUser::addSessionMessage(sprintf(LANG_CP_USER_DELETED, $user['nickname']), 'success');

        }

        $this->redirectBack();

    }

}
