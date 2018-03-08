<?php

class actionUsersSubscribe extends cmsAction {

    public function run($friend_id){

		if(!$this->cms_user->is_logged){ cmsCore::error404(); }

        if (!$friend_id) { cmsCore::error404(); }

        if ($this->cms_user->isSubscribe($friend_id)){ return false; }

        $friend = $this->model->getUser($friend_id);
        if (!$friend || $friend['is_locked']){ cmsCore::error404(); }

        if ($this->request->isAjax()){

            return $this->cms_template->renderAsset('ui/confirm', array(
                'confirm_title'  => sprintf(LANG_USERS_SUBSCRIBE_CONFIRM, $friend['nickname']),
                'confirm_action' => $this->cms_template->href_to('subscribe', $friend['id'])
            ));

        }

        $csrf_token = $this->request->get('csrf_token', '');

        if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

        $this->model->addFriendship($this->cms_user->id, $friend_id);

        cmsUser::addSessionMessage(LANG_USERS_SUBSCRIBE_SUCCESS);

        $this->redirectToAction($friend_id);

    }

}
