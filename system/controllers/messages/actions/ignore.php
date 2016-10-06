<?php

class actionMessagesIgnore extends cmsAction {

    public function run(){

        $contact_id = $this->request->get('contact_id', 0);

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        if (!$contact){
            $this->cms_template->renderJSON(array('error' => true));
        }

        $this->model->ignoreContact($this->cms_user->id, $contact_id);
        $this->model->deleteContact($this->cms_user->id, $contact_id);

        $count = $this->model->getContactsCount($this->cms_user->id);

        $this->cms_template->renderJSON(array(
            'error' => false,
            'count' => $count
        ));

    }

}
