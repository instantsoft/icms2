<?php

class actionMessagesContact extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        $contact_id = $this->request->get('contact_id', 0);
        if (!$contact_id) {
            return cmsCore::error404();
        }

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);
        if (!$contact) {
            return cmsCore::error404();
        }

        // чтобы не считать общее кол-во, получаем на один больше
        $messages = $this->model->
                limit($this->options['limit'] + 1)->
                getMessages($this->cms_user->id, $contact_id);

        list($messages, $contact) = cmsEventsManager::hook('messages_before_list', [$messages, $contact]);

        if (count($messages) > $this->options['limit']) {
            $has_older = true;
            array_shift($messages);
        } else {
            $has_older = false;
        }

        $this->model->setMessagesReaded($this->cms_user->id, $contact_id);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        $editor_params['options']['id'] = 'content';

        return $this->cms_template->render('contact', [
            'user'          => $this->cms_user,
            'editor_params' => $editor_params,
            'is_me_ignored' => $this->model->isContactIgnored($contact_id, $this->cms_user->id),
            'is_private'    => !$this->cms_user->isPrivacyAllowed($contact, 'messages_pm') && !$messages,
            'contact'       => $contact,
            'has_older'     => $has_older,
            'messages'      => $messages
        ]);
    }

}
