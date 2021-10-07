<?php

class actionUsersKeepInSubscribers extends cmsAction {

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

                $this->model->keepInSubscribers($this->cms_user->id, $friend_id);

                cmsUser::addSessionMessage(sprintf(LANG_USERS_FRIENDS_DELETED, $friend['nickname']));

                $this->sendNoticeDeleted($friend);

                $back_url = $this->getRequestBackUrl();

                if ($back_url){
                    $this->redirect($back_url);
                }

                $this->redirectToAction($friend_id);

            } else {

                return $this->cms_template->renderAsset('ui/confirm', array(
                    'confirm_title'  => sprintf(LANG_USERS_FRIENDS_SUBSCRIBE_CONFIRM, $friend['nickname']),
                    'confirm_action' => $this->cms_template->href_to('keep_in_subscribers', $friend['id'])
                ), $this->request);

            }

        }

        //
        // Запрос из уведомления (внутренний)
        //
        if ($this->request->isInternal()){

            $this->model->keepInSubscribers($this->cms_user->id, $friend_id);

            $this->sendNoticeDeleted($friend);

            return true;

        }

    }

    private function sendNoticeDeleted($friend){

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        $sender_link = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';

        $notice = array(
            'content' => sprintf(LANG_USERS_KEEP_IN_SUBSCRIBERS_NOTICE, $sender_link)
        );

        $messenger->sendNoticePM($notice, 'users_friend_delete');

        return true;

    }

}
