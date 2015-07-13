<?php

class actionMessagesShowOlder extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $contact_id = $this->request->get('contact_id') or cmsCore::error404();
        $message_id = $this->request->get('message_id') or cmsCore::error404();

        $contact = $this->model->getContact($user->id, $contact_id);

        if (!$contact){
            $template->renderJSON(array('error' => true));
        }

        $messages = $this->model->filterLt('id', $message_id)->
                                    limit($this->options['limit'])->
                                    getMessages($user->id, $contact_id);

        $messages_html = $template->render('message', array(
            'messages' => $messages,
            'user'=>$user
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $first_message_id = $messages[0]['id'];

        $has_older = $this->model->hasOlderMessages($user->id, $contact_id, $first_message_id);

        $template->renderJSON(array(
            'error' => $messages ? false : true,
            'html' => $messages ? $messages_html : '',
            'has_older' => $has_older,
            'older_id' => $first_message_id
        ));

    }

}
