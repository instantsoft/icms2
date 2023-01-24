<?php

class actionAdminMessagesNotices extends cmsAction {

    protected $extended_langs = ['messages'];

    public function run() {

        $this->cms_template->render('notices', [
            'user'    => $this->cms_user,
            'notices' => $this->model_messages->getNotices($this->cms_user->id)
        ]);
    }

}
