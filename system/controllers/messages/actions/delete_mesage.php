<?php

class actionMessagesDeleteMesage extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $_message_ids = $this->request->get('message_ids', []);
        if (!$_message_ids) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        foreach ($_message_ids as $message_id) {
            $message_ids[] = intval($message_id);
        }

        $delete_msg_ids = $this->model->deleteMessages($this->cms_user->id, $message_ids);

        if ($delete_msg_ids) {
            $message_ids = array_diff($message_ids, $delete_msg_ids);
        }

        $this->cms_template->renderJSON([
            'error'          => false,
            'delete_text'    => LANG_PM_IS_DELETE . LANG_PM_DO_RESTORE,
            'remove_text'    => LANG_PM_IS_DELETE,
            'message_ids'    => $message_ids,
            'delete_msg_ids' => $delete_msg_ids
        ]);
    }

}
