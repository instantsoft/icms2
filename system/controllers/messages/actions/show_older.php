<?php

class actionMessagesShowOlder extends cmsAction {

    /**
     * @var array Описание правил валидации входных данных
     */
    public $request_params = array(
        'contact_id' => array(
            'default' => 0,
            'rules'   => array(
                array('required'),
                array('digits')
            )
        ),
        'message_id' => array(
            'default' => 0,
            'rules'   => array(
                array('required'),
                array('digits')
            )
        )
    );

    public function run(){

        $contact_id = $this->request->get('contact_id');
        $message_id = $this->request->get('message_id');

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        if (!$contact){
            $this->cms_template->renderJSON(array('error' => true));
        }

        $messages = $this->model->filterLt('id', $message_id)->
                                    limit($this->options['limit']+1)->
                                    getMessages($this->cms_user->id, $contact_id);

        if(count($messages) > $this->options['limit']){
            $has_older = true; array_shift($messages);
        } else {
            $has_older = false;
        }

        $this->cms_template->renderJSON(array(
            'error'     => ($messages ? false : true),
            'html'      => ($messages ? $this->cms_template->render('message', array(
                'messages'  => $messages,
                'last_date' => '',
                'user'      => $this->cms_user
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)) : ''),
            'has_older' => $has_older,
            'older_id'  => $messages[0]['id']
        ));

    }

}
