<?php

class actionMessagesRefresh extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $contact_id = $this->request->get('contact_id') or cmsCore::error404();

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $contact = $this->model->getContact($user->id, $contact_id);

        if (!$contact){ $template->renderJSON(array('error' => true)); }

        $messages = $this->model->filterEqual('is_new', 1)->getMessagesFromContact($user->id, $contact_id);

        if ($messages){

            $messages_html = $template->render('message', array(
                'messages' => $messages,
                'user'=>$user
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

            $this->model->setMessagesReaded($user->id, $contact_id);

        }

        $template->renderJSON(array(
            'error' => false,
            'html' => $messages? $messages_html : false
        ));

    }

}
