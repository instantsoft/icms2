<?php

class actionMessagesDelete extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $contact_id = $this->request->get('contact_id');

        $contact = $this->model->getContact($user->id, $contact_id);

        if (!$contact){
            $template->renderJSON(array('error' => true));
        }

        $this->model->deleteContact($user->id, $contact_id);

        $count = $this->model->getContactsCount($user->id);

        $template->renderJSON(array(
            'error' => false,
            'count' => $count
        ));

    }

}