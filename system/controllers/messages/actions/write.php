<?php

class actionMessagesWrite extends cmsAction {

    public function run($contact_id) {

        if (!is_numeric($contact_id)) {
            return cmsCore::error404();
        }

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        // Самому себе нельзя
        if ($this->cms_user->id == $contact_id) {
            return cmsCore::error404();
        }

        $contact_exists_id = $this->model->isContactExists($this->cms_user->id, $contact_id);

        if (!$contact_exists_id) {
            $contact_exists_id = $contact_id;
            $this->model->addContact($this->cms_user->id, $contact_id);
        }

        // Какой контакт выбираем
        $this->request->set('select_contact_id', $contact_exists_id);

        $this->executeAction('index');
    }

}
