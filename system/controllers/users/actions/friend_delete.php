<?php

class actionUsersFriendDelete extends cmsAction {

    public function run($friend_id){

		if (!$this->cms_user->is_logged) { cmsCore::error404(); }

        if (!$friend_id) { cmsCore::error404(); }

        if (!$this->model->isFriendshipExists($this->cms_user->id, $friend_id)){ return false; }

        $friend = $this->model->getUser($friend_id);
        if (!$friend || $friend['is_locked']){ cmsCore::error404(); }

        //
        // Запрос по ссылке из профиля
        //
        if ($this->request->isStandard() || $this->request->isAjax()){

            if ($this->request->has('submit')){

                // подтвержение получено

                $csrf_token = $this->request->get('csrf_token', '');

                if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

                $this->model->deleteFriendship($this->cms_user->id, $friend_id);

                cmsUser::addSessionMessage(sprintf(LANG_USERS_FRIENDS_DELETED, $friend['nickname']));

                $this->sendNoticeDeleted($friend);

                $back_url = $this->getRequestBackUrl();

                if ($back_url){
                    $this->redirect($back_url);
                }

                $this->redirectToAction($friend_id);

            } else {

                return $this->cms_template->renderAsset('ui/confirm', array(
                    'confirm_title'  => sprintf(LANG_USERS_FRIENDS_DELETE_CONFIRM, $friend['nickname']),
                    'confirm_action' => $this->cms_template->href_to('friend_delete', $friend['id'])
                ), $this->request);

            }

        }

        //
        // Запрос из уведомления (внутренний)
        //
        if ($this->request->isInternal()){

            $this->model->deleteFriendship($this->cms_user->id, $friend_id);

            $this->sendNoticeDeleted($friend, true);

            return true;

        }

    }

    public function sendNoticeDeleted($friend, $is_declined=false){

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        $sender_link = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';

        $content = $is_declined ?
                    sprintf(LANG_USERS_FRIENDS_DECLINED, $sender_link) :
                    sprintf(LANG_USERS_FRIENDS_UNDONE, $sender_link);

        $notice = array(
            'content' => $content,
        );

        $messenger->sendNoticePM($notice, 'users_friend_delete');

        //
        // E-mail
        //
        if (!$is_declined){
            $messenger->sendNoticeEmail('users_friend_delete', array(
                'friend_nickname' => $this->cms_user->nickname,
                'friend_url'      => href_to_profile($this->cms_user, false, true)
            ));
        }

        return true;

    }

}
