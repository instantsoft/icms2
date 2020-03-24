<?php

class actionMessagesSend extends cmsAction {

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
        'content' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('max_length', 65535)
            )
        ),
        'csrf_token' => array(
            'default' => '',
            'rules'   => array(
                array('required')
            )
        ),
        'last_date' => array(
            'default' => '',
            'rules'   => array(
                array('regexp', "/^([a-z0-9 ]*)$/ui")
            )
        )
    );

    public function run(){

        $contact_id = $this->request->get('contact_id');
        $content    = $this->request->get('content');
        $last_date  = $this->request->get('last_date');

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token'))){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => ''
            ));
        }

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        // Контакт существует?
        if (!$contact){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => ''
            ));
        }

        // Контакт не в игноре у отправителя?
        if ($contact['is_ignored']){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_PM_CONTACT_IS_IGNORED
            ));
        }

        // Отправитель не в игноре у контакта?
        if ($this->model->isContactIgnored($contact_id, $this->cms_user->id)){
            $this->cms_template->renderJSON(array(
                'error' => true,
                'message' => LANG_PM_YOU_ARE_IGNORED
            ));
        }

        // Контакт принимает сообщения от этого пользователя?
        if (!$this->cms_user->isPrivacyAllowed($contact, 'messages_pm')){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_PM_CONTACT_IS_PRIVATE
            ));
        }

        //
        // Отправляем сообщение
        //
        $is_textarea_editor = !$this->options['editor'] || $this->options['editor'] == 'markitup';

        $content_html = cmsEventsManager::hook('html_filter', [
            'text' => $content,
            'is_auto_br' => $is_textarea_editor,
            'build_smiles' => $is_textarea_editor
        ]);

		if (!$content_html) {
			$this->cms_template->renderJSON(array(
	            'error'   => true,
                'date'    => false,
                'message' => LANG_PM_SEND_ERROR
            ));
		}

        $this->setSender($this->cms_user->id)->addRecipient($contact_id);

        $message_id = $this->sendMessage($content_html);

        //
        // Отправляем уведомление на почту
        //
        $user_to = cmsCore::getModel('users')->getUser($contact_id);

        if (!$user_to['is_online']) {

            if($this->model->getNewMessagesCount($user_to['id']) == 1){
                $this->sendNoticeEmail('messages_new', array(
                    'user_url'      => href_to_abs('users', $this->cms_user->id),
                    'user_nickname' => $this->cms_user->nickname,
                    'message'       => strip_tags($content_html)
                ));
            }

        }

        //
        // Получаем и рендерим добавленное сообщение
        //
        $message = $this->model->getMessage($message_id);

        $message_html = $this->cms_template->render('message', array(
            'messages'  => array($message),
            'last_date' => $last_date,
            'user'      => $this->cms_user
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        // Результат
        $this->cms_template->renderJSON(array(
            'error'   => false,
            'date'    => date($this->cms_config->date_format, time()),
            'message' => $message_html
        ));

    }

}
