<?php

class actionMessagesRestoreMesage extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $message_id = $this->request->get('message_id', 0);
        if (!$message_id) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $this->model->restoreMessages($this->cms_user->id, $message_id);

        $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

}
