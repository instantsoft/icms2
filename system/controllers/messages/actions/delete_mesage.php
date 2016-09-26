<?php

class actionMessagesDeleteMesage extends cmsAction {

    public function run(){

        $_message_ids = $this->request->get('message_ids', array());
        if (!$_message_ids){ $this->cms_template->renderJSON(array('error' => true)); }

        foreach ($_message_ids as $message_id) {
            $message_ids[] = (int)$message_id;
        }

        $this->model->deleteMessages($this->cms_user->id, $message_ids);

        $this->cms_template->renderJSON(array(
            'error'       => false,
            'delete_text' => LANG_PM_IS_DELETE,
            'message_ids' => $message_ids
        ));

    }

}
