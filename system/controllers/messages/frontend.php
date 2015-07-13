<?php
class messages extends cmsFrontend {

    protected $useOptions = true;

    private $sender_id;
    private $recipients = array();
    private $is_ignore_options = false;

    /**
     * Устанавливает отправителя сообщения
     * @param int $user_id
     */
    public function setSender($user_id){

        $this->sender_id = $user_id;

    }

    /**
     * Добавляет получателя сообщения
     * @param int $user_id
     */
    public function addRecipient($user_id){

        $this->recipients[] = $user_id;

    }

    /**
     * Добавляет список получателей сообщения
     * @param array $list
     */
    public function addRecipients($list){

        $this->recipients = array_merge($this->recipients, $list);

    }

    public function clearRecipients(){
        $this->recipients = array();
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

        // Обновляем даты последних сообщений в контактах
        if ($message_id){
            foreach($this->recipients as $contact_id){
                $this->model->updateContactsDateLastMsg($this->sender_id, $contact_id);
            }
        }

        return $message_id ? $message_id : false;

    }

    public function ignoreNotifyOptions(){
        $this->is_ignore_options = true;
        return $this;
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

            return $this->model->addNotice($this->recipients, $notice);

        }

        if ($notice_type){

            $options_only = $this->is_ignore_options ? false : array('pm', 'both');
            $recipients = cmsCore::getModel('users')->getNotifiedUsers($notice_type, $this->recipients, $options_only);

            if (!$recipients) { return false; }

            $this->is_ignore_options = false;

            return $this->model->addNotice($recipients, $notice);

        }

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

        $mailer = new cmsMailer();

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

    public function sendEmail($to, $letter, $data=array()){

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
            'date' => html_date(time()),
            'time' => html_time(time()),
        ), $data);

        $letter['text'] = string_replace_keys_values($letter['text'], $data);

        $mailer = new cmsMailer();

        $mailer->addTo($to['email'], $to['name']);

        $letter['text'] = $mailer->parseSubject($letter['text']);
        $letter['text'] = $mailer->parseAttachments($letter['text']);

        $mailer->setBodyHTML( nl2br($letter['text']) );

        $result = $mailer->send();

        $mailer->clearTo()->clearAttachments();

        return $result;

    }

}
