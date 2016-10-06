<?php

class actionMessagesNotices extends cmsAction {

    public function run(){

        $this->cms_template->render('notices', array(
            'user'    => $this->cms_user,
            'notices' => $this->model->getNotices($this->cms_user->id)
        ));

    }

}
