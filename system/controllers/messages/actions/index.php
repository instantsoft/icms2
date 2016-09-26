<?php

class actionMessagesIndex extends cmsAction {

    public function run(){

        $is_allowed = $this->cms_user->isInGroups( $this->options['groups_allowed'] );

        $contacts = $this->model->getContacts($this->cms_user->id);

        $this->cms_template->render('index', array(
            'user'       => $this->cms_user,
            'is_allowed' => $is_allowed,
            'contacts'   => $contacts
        ));

    }

}
