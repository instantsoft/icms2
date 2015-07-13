<?php

class actionUsersFriendDelete extends cmsAction {

    public function run($friend_id){
		
		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        $user = cmsUser::getInstance();

        if (!$friend_id) { cmsCore::error404(); }

        if (!$this->model->isFriendshipExists($user->id, $friend_id)){ return false; }

        $friend = $this->model->getUser($friend_id);

        if (!$friend){ cmsCore::error404(); }

        //
        // Запрос по ссылке из профиля
        //
        if ($this->request->isStandard()){

            if ($this->request->has('submit')){

                // подтвержение получено

                $csrf_token = $this->request->get('csrf_token');

                if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

                $this->model->deleteFriendship($user->id, $friend_id);

                cmsUser::addSessionMessage(sprintf(LANG_USERS_FRIENDS_DELETED, $friend['nickname']));

                $this->sendNoticeDeleted($friend);

                $this->redirectToAction($friend_id);

            } else {

                // спрашиваем подтверждение

                return cmsTemplate::getInstance()->render('friend_delete', array(
                    'user' => $user,
                    'friend' => $friend,
                ));

            }

        }

        //
        // Запрос из уведомления (внутренний)
        //
        if ($this->request->isInternal()){

            $this->model->deleteFriendship($user->id, $friend_id);

            $this->sendNoticeDeleted($friend, true);

            return true;

        }

    }

    public function sendNoticeDeleted($friend, $is_declined=false){

        $user = cmsUser::getInstance();

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        $sender_link = '<a href="'.href_to($this->name, $user->id).'">'.$user->nickname.'</a>';

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
                'friend_nickname' => $user->nickname,
                'friend_url' => href_to_abs('users', $user->id),
            ));
        }

        return true;

    }

}
