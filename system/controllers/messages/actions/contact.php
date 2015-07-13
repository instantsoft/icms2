<?php

class actionMessagesContact extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $contact_id = $this->request->get('contact_id') or cmsCore::error404();

        $contact = $this->model->getContact($user->id, $contact_id);

        $messages = $this->model->limit($this->options['limit'])->getMessages($user->id, $contact_id);

        $first_message_id = $messages[0]['id'];

        $has_older = $this->model->hasOlderMessages($user->id, $contact_id, $first_message_id);

        $this->model->setMessagesReaded($user->id, $contact_id);

        cmsTemplate::getInstance()->render('contact', array(
            'user' => $user,
            'is_me_ignored' => $this->model->isContactIgnored($contact_id, $user->id),
            'is_private' => !$user->isPrivacyAllowed($contact, 'messages_pm'),
            'contact' => $contact,
            'has_older' => $has_older,
            'messages' => $messages
        ));

    }

}
