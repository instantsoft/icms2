<?php

class actionMessagesIndex extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $is_allowed = $this->cms_user->isInGroups($this->options['groups_allowed']);

        $contacts = $this->model->getContacts($this->cms_user->id);

        $select_contact_id = false;
        // Есть контакты и разрешено выбирать сразу первый из списка
        if(!empty($contacts[0]['id']) && !empty($this->options['is_contact_first_select'])){
            $select_contact_id = $contacts[0]['id'];
        }
        // Когда мы пишем сразу целевому юзеру, то выбираем его контакт
        if($this->request->has('select_contact_id')){
            $select_contact_id = $this->request->get('select_contact_id', 0);
        }

        $this->cms_template->render('index', [
            'is_modal'          => intval($this->request->isAjax()),
            'select_contact_id' => $select_contact_id,
            'user'              => $this->cms_user,
            'is_allowed'        => $is_allowed,
            'refresh_time'      => (!empty($this->options['refresh_time']) ? ($this->options['refresh_time'] * 1000) : 15000),
            'contacts'          => $contacts
        ]);
    }

}
