<?php

class actionMessagesForgive extends cmsAction {

    public function run(){

        $user     = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $contact_id = $this->request->get('contact_id');

        $contact = $this->model->getContact($user->id, $contact_id);

        if (!$contact){
            $template->renderJSON(array('error' => true));
        }

        $this->model->forgiveContact($user->id, $contact_id);

        $template->renderJSON(array(
            'error' => false
        ));

    }

}