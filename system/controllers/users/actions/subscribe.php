<?php

class actionUsersSubscribe extends cmsAction {

    public function run($friend_id) {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if (!$friend_id) {
            return cmsCore::error404();
        }

        if ($this->cms_user->isSubscribe($friend_id)) {
            return cmsCore::error404();
        }

        $friend = $this->model->getUser($friend_id);
        if (!$friend || $friend['is_locked']) {
            return cmsCore::error404();
        }

        if ($this->request->isAjax()) {

            return $this->cms_template->renderAsset('ui/confirm', [
                'confirm_title'  => sprintf(LANG_USERS_SUBSCRIBE_CONFIRM, $friend['nickname']),
                'confirm_action' => $this->cms_template->href_to('subscribe', $friend['id'])
            ], $this->request);
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->subscribeUser($this->cms_user->id, $friend['id']);

        $this->controller_messages->addRecipient($friend['id']);

        $sender_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';

        $notice = [
            'content' => sprintf(LANG_USERS_SUBSCRIBE_DONE, $sender_link),
        ];

        $this->controller_messages->sendNoticePM($notice);

        cmsUser::addSessionMessage(LANG_USERS_SUBSCRIBE_SUCCESS);

        return $this->redirectToAction($friend['id']);
    }

}
