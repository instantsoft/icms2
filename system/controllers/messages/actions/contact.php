<?php

class actionMessagesContact extends cmsAction {

    public function run(){

        $contact_id = $this->request->get('contact_id', 0) or cmsCore::error404();

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        // чтобы не считать общее кол-во, получаем на один больше
        $messages = $this->model->limit($this->options['limit']+1)->getMessages($this->cms_user->id, $contact_id);

        if(count($messages) > $this->options['limit']){
            $has_older = true; array_shift($messages);
        } else {
            $has_older = false;
        }

        $this->model->setMessagesReaded($this->cms_user->id, $contact_id);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        $this->cms_template->render('contact', array(
            'user'          => $this->cms_user,
            'editor_params' => $editor_params,
            'is_me_ignored' => $this->model->isContactIgnored($contact_id, $this->cms_user->id),
            'is_private'    => !$this->cms_user->isPrivacyAllowed($contact, 'messages_pm'),
            'contact'       => $contact,
            'has_older'     => $has_older,
            'messages'      => $messages
        ));

    }

}
