<?php

class actionMessagesNotices extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        $notices = $this->model->getNotices($user->id);

        cmsTemplate::getInstance()->render('notices', array(
            'user' => $user,
            'notices' => $notices
        ));

    }

}