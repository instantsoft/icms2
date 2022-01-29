<?php

class actionUsersFriendAdd extends cmsAction {

    public function run($friend_id) {

        if (!$this->cms_user->is_logged) {
            return $this->errorContext();
        }

        if (!$friend_id) {
            return $this->errorContext();
        }

        if ($this->cms_user->isFriend($friend_id)) {
            return $this->errorContext();
        }

        $friend = $this->model->getUser($friend_id);
        if (!$friend || $friend['is_locked']) {
            return $this->errorContext();
        }

        //
        // Запрос по ссылке из профиля
        //
        if ($this->request->isStandard() || $this->request->isAjax()) {

            if (!$this->cms_user->isPrivacyAllowed($friend, 'users_friendship', true)) {
                return cmsCore::error404();
            }

            //
            // Если запрос от друга уже существует
            //
            $friendship_requested = $this->model->getFriendshipRequested($friend['id'], $this->cms_user->id);

            if ($friendship_requested !== false) {

                // если в подписчиках, спрашиваем подтверждение
                if ($friendship_requested === null && !$this->request->has('submit')) {
                    return $this->cms_template->renderAsset('ui/confirm', [
                        'confirm_title'  => LANG_USERS_FRIENDS_ADD . '?',
                        'confirm_action' => $this->cms_template->href_to('friend_add', $friend['id'])
                    ], $this->request);
                }

                $this->model->addFriendship($this->cms_user->id, $friend['id']);

                cmsUser::addSessionMessage(sprintf(LANG_USERS_FRIENDS_DONE, $friend['nickname']), 'success');

                $this->sendNoticeAccepted($friend);

                $this->redirectToAction($friend['id']);
            }

            //
            // Если запроса от друга не было
            //
            if ($this->request->has('submit')) {

                // подтвержение получено

                $csrf_token = $this->request->get('csrf_token', '');

                if (!cmsForm::validateCSRFToken($csrf_token)) {
                    return cmsCore::error404();
                }

                $this->model->addFriendship($this->cms_user->id, $friend['id']);

                cmsUser::addSessionMessage(LANG_USERS_FRIENDS_SENT);

                $this->sendNoticeRequest($friend);

                $this->redirectToAction($friend['id']);
            } else {

                // спрашиваем подтверждение
                return $this->cms_template->renderAsset('ui/confirm', [
                    'confirm_title'  => sprintf(LANG_USERS_FRIENDS_CONFIRM, $friend['nickname']),
                    'confirm_action' => $this->cms_template->href_to('friend_add', $friend['id'])
                ], $this->request);
            }
        }

        //
        // Запрос из уведомления (внутренний)
        //
        if ($this->request->isInternal()) {

            $this->model->addFriendship($this->cms_user->id, $friend['id']);

            $this->sendNoticeAccepted($friend);

            return true;
        }
    }

    public function sendNoticeRequest($friend) {

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        //
        // Личное сообщение
        //
        $sender_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';

        $notice = [
            'content' => sprintf(LANG_USERS_FRIENDS_NOTICE, $sender_link),
            'options' => [
                'is_closeable' => false
            ],
            'actions' => [
                'accept'  => [
                    'title'      => LANG_ACCEPT,
                    'controller' => $this->name,
                    'action'     => 'friend_add',
                    'params'     => [$this->cms_user->id]
                ],
                'keep'    => [
                    'title'      => LANG_USERS_KEEP_IN_SUBSCRIBERS,
                    'controller' => $this->name,
                    'action'     => 'keep_in_subscribers',
                    'params'     => [$this->cms_user->id]
                ],
                'decline' => [
                    'title'      => LANG_DECLINE,
                    'controller' => $this->name,
                    'action'     => 'friend_delete',
                    'params'     => [$this->cms_user->id]
                ]
            ]
        ];

        // личное сообщение посылаем всегда, независимо от настроек уведомлений пользователя
        $messenger->ignoreNotifyOptions()->sendNoticePM($notice, 'users_friend_add');

        //
        // E-mail
        //
        $messenger->sendNoticeEmail('users_friend_add', [
            'friend_nickname' => $this->cms_user->nickname,
            'friend_url'      => href_to_profile($this->cms_user, false, true)
        ]);
    }

    public function sendNoticeAccepted($friend) {

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($friend['id']);

        $sender_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';

        $notice = [
            'content' => sprintf(LANG_USERS_FRIENDS_DONE, $sender_link),
        ];

        return $messenger->sendNoticePM($notice, 'users_friend_accept');
    }

    private function errorContext() {

        if ($this->request->isInternal()) {
            return false;
        }

        return cmsCore::error404();
    }

}
