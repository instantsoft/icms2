<?php

class actionUsersFriendAdd extends cmsAction {

    public function run($friend_id){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }
		
        $user = cmsUser::getInstance();

        if (!$friend_id) { cmsCore::error404(); }

        if ($user->isFriend($friend_id)){ return false; }

        $friend = $this->model->getUser($friend_id);

        if (!$friend){ cmsCore::error404(); }

        //
        // Запрос по ссылке из профиля
        //
        if ($this->request->isStandard()){

            //
            // Если запрос от друга уже существует
            //

            if ($this->model->isFriendshipRequested($friend_id, $user->id)){

                $this->model->addFriendship($user->id, $friend_id);

                cmsUser::addSessionMessage(sprintf(LANG_USERS_FRIENDS_DONE, $friend['nickname']), 'success');

                $this->sendNoticeAccepted($friend);

                $this->redirectToAction($friend_id);

            }

            //
            // Если запроса от друга не было
            //

            if ($this->request->has('submit')){

                // подтвержение получено

                $csrf_token = $this->request->get('csrf_token');

                if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

                $this->model->addFriendship($user->id, $friend_id);

                cmsUser::addSessionMessage(LANG_USERS_FRIENDS_SENT);

                $this->sendNoticeRequest($friend);

                $this->redirectToAction($friend_id);

            } else {

                // спрашиваем подтверждение

                return cmsTemplate::getInstance()->render('friend_add', array(
                    'user' => $user,
                    'friend' => $friend,
                ));

            }

        }

        //
        // Запрос из уведомления (внутренний)
        //
        if ($this->request->isInternal()){

            $this->model->addFriendship($user->id, $friend_id);

            $this->sendNoticeAccepted($friend);

            return true;

        }

    }

    public function sendNoticeRequest($friend){

        $user = cmsUser::getInstance();

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        //
        // Личное сообщение
        //
        $sender_link = '<a href="'.href_to($this->name, $user->id).'">'.$user->nickname.'</a>';

        $notice = array(
            'content' => sprintf(LANG_USERS_FRIENDS_NOTICE, $sender_link),
            'options' => array(
                'is_closeable' => false
            ),
            'actions' => array(
                'accept' => array(
                    'title' => LANG_ACCEPT,
                    'controller' => $this->name,
                    'action' => 'friend_add',
                    'params' => array($user->id),
                ),
                'decline' => array(
                    'title' => LANG_DECLINE,
                    'controller' => $this->name,
                    'action' => 'friend_delete',
                    'params' => array($user->id),
                )
            )
        );

        // личное сообщение посылаем всегда, независимо от настроек уведомлений пользователя
        $messenger->ignoreNotifyOptions()->sendNoticePM($notice, 'users_friend_add');

        //
        // E-mail
        //
        $messenger->sendNoticeEmail('users_friend_add', array(
            'friend_nickname' => $user->nickname,
            'friend_url' => href_to_abs('users', $user->id),
        ));

    }

    public function sendNoticeAccepted($friend){

        $user = cmsUser::getInstance();

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        $sender_link = '<a href="'.href_to($this->name, $user->id).'">'.$user->nickname.'</a>';

        $notice = array(
            'content' => sprintf(LANG_USERS_FRIENDS_DONE, $sender_link),
        );

        $messenger->sendNoticePM($notice, 'users_friend_aссept');

    }

}
