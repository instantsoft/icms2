<?php
class messages extends cmsFrontend {

    protected $useOptions = true;

    private $sender_id;
    private $recipients = array();
    private $is_ignore_options = false;

    /**
     * Все запросы могут быть выполнены только авторизованными и только по аякс
     * @param type $action_name
     */
    public function before($action_name) {

        parent::before($action_name);

        if(!$this->request->isInternal()){

            if (!$this->request->isAjax()){ cmsCore::error404(); }

            if (!cmsUser::isLogged()){ cmsCore::error404(); }

        }

        return true;

    }

    /**
     * Устанавливает отправителя сообщения
     * @param int $user_id
     */
    public function setSender($user_id){
        $this->sender_id = $user_id; return $this;
    }

    /**
     * Добавляет получателя сообщения
     * @param int $user_id
     */
    public function addRecipient($user_id){
        $this->recipients[] = $user_id; return $this;
    }

    /**
     * Добавляет список получателей сообщения
     * @param array $list
     */
    public function addRecipients($list){
        $this->recipients = array_merge($this->recipients, $list); return $this;
    }

    public function clearRecipients(){
        $this->recipients = array(); return $this;
    }

    /**
     * Отправляет сообщение
     * @param string $content
     * @return int | false
     */
    public function sendMessage($content){

        // Создаем контакты
        foreach($this->recipients as $contact_id){
            if (!$this->model->isContactExists($contact_id, $this->sender_id)){
                $this->model->addContact($contact_id, $this->sender_id);
            }
        }

        // Сохраняем сообщение
        $message_id = $this->model->addMessage($this->sender_id, $this->recipients, $content);

        if ($message_id){

            // Обновляем даты последних сообщений в контактах
            foreach($this->recipients as $contact_id){
                $this->model->updateContactsDateLastMsg($this->sender_id, $contact_id);
            }

            cmsEventsManager::hook('send_user_message', array($this->sender_id, $this->recipients, $content));

        }

        return $message_id ? $message_id : false;

    }

    public function ignoreNotifyOptions(){
        $this->is_ignore_options = true; return $this;
    }

    /**
     * Отправляет уведомление через личные сообщения
     * @param array $notice
     * @param string $notice_type
     * @return int | false
     */
    public function sendNoticePM($notice, $notice_type=false){

        if (!$notice_type){

            if (!$this->recipients){ return; }

            $notice_id = $this->model->addNotice($this->recipients, $notice);

        } else {

            $options_only = $this->is_ignore_options ? false : array('pm', 'both');
            $recipients = cmsCore::getModel('users')->getNotifiedUsers($notice_type, $this->recipients, $options_only);

            if (!$recipients) { return false; }

            $this->is_ignore_options = false;

            $notice_id = $this->model->addNotice($recipients, $notice);

        }

        cmsEventsManager::hook('send_user_notice', array((isset($recipients) ? $recipients : $this->recipients), $notice));

        return $notice_id;

    }

    /**
     * Отправляет email-уведомления указанного типа всем
     * подписанным пользователям
     * @param string $notice_type
     * @param string $notice Массив ключей и значений для замены в тексте письма
     * @return boolean
     */
    public function sendNoticeEmail($notice_type, $notice=array()){

        if (!$this->recipients){ return; }

        $letter_text = cmsCore::getLanguageTextFile("letters/{$notice_type}");
        if (!$letter_text){ return false; }

        $options_only = $this->is_ignore_options ? false : array('email', 'both');
        $recipients = cmsCore::getModel('users')->getNotifiedUsers($notice_type, $this->recipients, $options_only);
        if (!$recipients) { return false; }

        $this->is_ignore_options = false;

        $letter_text = string_replace_keys_values($letter_text, $notice);

        $success = true;

        foreach($recipients as $recipient){

            $to = array(
                'name' => $recipient['nickname'],
                'email' => $recipient['email']
            );

            $letter = array(
                'text' => string_replace_keys_values($letter_text, $recipient)
            );

            $success = $success && $this->sendEmail($to, $letter);

        }

        return $success;

    }

    public function sendEmail($to, $letter, $data=array(), $is_nl2br_text=true){

		if (is_array($to)){
			if (empty($to['email'])) { return false; }
			if (empty($to['name'])){ $to['name'] = false; }
		} else {
			if (empty($to)) { return false; }
			$to = array('email' => $to, 'name' => false);
		}

		if (is_array($letter)){
			if (empty($letter['text'])){ $letter['text'] = cmsCore::getLanguageTextFile("letters/{$letter['name']}"); }
		} else {
			$letter = array('text' => cmsCore::getLanguageTextFile("letters/{$letter}"));
		}

        if (!$letter['text']){ return false; }

        $config = cmsConfig::getInstance();

        $data = array_merge(array(
            'site' => $config->sitename,
            'date' => html_date(),
            'time' => html_time()
        ), $data);

        $letter['text'] = string_replace_keys_values($letter['text'], $data);

        $before_send = cmsEventsManager::hook('before_send_email', array(
            'send_email' => true,
            'success'    => false,
            'to'         => $to,
            'letter'     => $letter
        ));

        if(!$before_send['send_email']){
            return $before_send['success'];
        }

        $mailer = new cmsMailer();

        $mailer->addTo($to['email'], $to['name']);

        $letter['text'] = $mailer->parseSubject($letter['text']);
        $letter['text'] = $mailer->parseAttachments($letter['text']);

        $mailer->setBodyHTML( ($is_nl2br_text ? nl2br($letter['text']) : $letter['text']) );

        $result = $mailer->send();

        $mailer->clearTo()->clearAttachments();

        return $result;

    }

}
