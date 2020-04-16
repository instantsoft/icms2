<?php

class actionMessagesIndex extends cmsAction {

    public function run(){

        $is_allowed = $this->cms_user->isInGroups( $this->options['groups_allowed'] );

        $contacts = $this->model->getContacts($this->cms_user->id);

        $this->cms_template->render('index', array(
            'is_modal'     => (int)$this->request->isAjax(),
            'user'         => $this->cms_user,
            'is_allowed'   => $is_allowed,
            'refresh_time' => (!empty($this->options['refresh_time']) ? ($this->options['refresh_time']*1000) : 15000),
            'contacts'     => $contacts
        ));

    }

}
