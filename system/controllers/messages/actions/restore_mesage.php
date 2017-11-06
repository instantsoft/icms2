<?php

class actionMessagesRestoreMesage extends cmsAction {

    public function run(){

        $message_id = $this->request->get('message_id', 0);
        if (!$message_id){ $this->cms_template->renderJSON(array('error' => true)); }

        $this->model->restoreMessages($this->cms_user->id, $message_id);

        $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
