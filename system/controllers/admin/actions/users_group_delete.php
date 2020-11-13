<?php

class actionAdminUsersGroupDelete extends cmsAction {

    public function run($id = false){

        if (!$id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $count = $this->model->filterNotEqual('id', GUEST_GROUP_ID)->getCount('{users}_groups');

        if($count <= 1){

            cmsUser::addSessionMessage(LANG_CP_USER_GROUP_ERR_DELETE, 'info');

            $this->redirectToAction('users');

        }

        $this->model_users->deleteGroup($id);

        cmsUser::unsetCookie('users_tree_path');

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('users');

    }

}
