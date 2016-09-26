<?php

class actionMessagesForgive extends cmsAction {

    public function run(){

        $contact_id = $this->request->get('contact_id', 0);

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        if (!$contact){
            $this->cms_template->renderJSON(array('error' => true));
        }

        $this->model->forgiveContact($this->cms_user->id, $contact_id);

        $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
