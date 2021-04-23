<?php

class actionMessagesIndex extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $is_allowed = $this->cms_user->isInGroups($this->options['groups_allowed']);

        $contacts = $this->model->getContacts($this->cms_user->id);

        // Выбирать первый контакт из списка?
        $is_contact_first_select = !empty($this->options['is_contact_first_select']);
        // Когда мы пишем сразу целевому юзеру, то выбираем контакт сразу
        if($this->request->has('contact_first_select')){
            $is_contact_first_select = $this->request->get('contact_first_select', 0);
        }

        $this->cms_template->render('index', [
            'is_modal'                => intval($this->request->isAjax()),
            'is_contact_first_select' => $is_contact_first_select,
            'user'                    => $this->cms_user,
            'is_allowed'              => $is_allowed,
            'refresh_time'            => (!empty($this->options['refresh_time']) ? ($this->options['refresh_time'] * 1000) : 15000),
            'contacts'                => $contacts
        ]);
    }

}
