<?php

class actionMessagesDelete extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $contact_id = $this->request->get('contact_id', 0);
        if (!$contact_id) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);
        if (!$contact) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $this->model->deleteContact($this->cms_user->id, $contact_id);

        $count = $this->model->getContactsCount($this->cms_user->id);

        $this->cms_template->renderJSON([
            'error' => false,
            'count' => $count
        ]);
    }

}
