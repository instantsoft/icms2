<?php

class actionMessagesIndex extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $is_allowed = $user->isInGroups( $this->options['groups_allowed'] );

        $contacts = $this->model->getContacts($user->id);

        cmsTemplate::getInstance()->render('index', array(
            'user' => $user,
            'is_allowed' => $is_allowed,
            'contacts' => $contacts
        ));

    }

}
