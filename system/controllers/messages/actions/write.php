<?php

class actionMessagesWrite extends cmsAction {

    public function run($contact_id){

        $user = cmsUser::getInstance();

        $is_contact_exists = $this->model->isContactExists($user->id, $contact_id);

        if ($is_contact_exists){
            $this->model->updateContactsDateLastMsg($user->id, $contact_id, false);
        }

        if (!$is_contact_exists){
            $this->model->addContact($user->id, $contact_id);
        }

        $this->runAction('index');

    }

}