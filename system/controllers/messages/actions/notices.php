<?php

class actionMessagesNotices extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $notices = $this->model->getNotices($user->id);

        cmsTemplate::getInstance()->render('notices', array(
            'user' => $user,
            'notices' => $notices
        ));

    }

}
