<?php

class actionMessagesWrite extends cmsAction {

    public function run($contact_id) {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $is_contact_exists = $this->model->isContactExists($this->cms_user->id, $contact_id);

        if ($is_contact_exists) {
            $this->model->updateContactsDateLastMsg($this->cms_user->id, $contact_id, false);
        }

        if (!$is_contact_exists) {
            $this->model->addContact($this->cms_user->id, $contact_id);
        }

        // Выбираем контакт сразу
        $this->request->set('contact_first_select', 1);

        $this->executeAction('index');
    }

}
