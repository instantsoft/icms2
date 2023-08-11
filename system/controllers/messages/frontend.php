<?php
/**
 * @property \modelMessages $model
 * @property \modelUsers $model_users
 */
class messages extends cmsFrontend {

    protected $useOptions = true;

    private $sender_id;
    private $recipients = [];
    private $is_ignore_options = false;

    /**
     * Все запросы могут быть выполнены только авторизованными и только по аякс
     *
     * @param string $action_name
     */
    public function before($action_name) {

        parent::before($action_name);

        if (!$this->request->isInternal()) {

            if (!$this->request->isAjax() && $action_name !== 'index') {
                return cmsCore::error404();
            }

            if (!cmsUser::isLogged()) {
                return cmsCore::error404();
            }

            // Здесь виджеты не нужны
            if ($action_name !== 'index') {
                $this->cms_template->widgets_rendered = true;
            }
        }

        return true;
    }

    /**
     * Устанавливает отправителя сообщения
     *
     * @param integer $user_id
     * @return \messages
     */
    public function setSender($user_id) {

        $this->sender_id = $user_id;

        return $this;
    }

    /**
     * Добавляет получателя сообщения
     *
     * @param integer $user_id
     * @return \messages
     */
    public function addRecipient($user_id) {

        $this->recipients[] = $user_id;

        return $this;
    }

    /**
     * Добавляет список получателей сообщения
     *
     * @param array $list
     * @return \messages
     */
    public function addRecipients($list) {

        $this->recipients = array_merge($this->recipients, $list);

        return $this;
    }

    /**
     * Очищает список получателей сообщения
     *
     * @return \messages
     */
    public function clearRecipients() {

        $this->recipients = [];

        return $this;
    }

    /**
     * Отправляет личное сообщение
     *
     * @param string $content
     * @return integer | false
     */
    public function sendMessage($content) {

        // Создаем контакты получателям
        foreach ($this->recipients as $key => $contact_id) {

            // Контакт сам с собой невозможен
            if ($contact_id == $this->sender_id) {

                unset($this->recipients[$key]);

                continue;
            }

            if (!$this->model->isContactExists($contact_id, $this->sender_id)) {
                $this->model->addContact($contact_id, $this->sender_id);
            }
        }

        // Сохраняем сообщение
        $message_id = $this->model->addMessage($this->sender_id, $this->recipients, $content);

        if ($message_id) {

            $message_id = cmsEventsManager::hook('messages_after_send', $message_id);

            // Обновляем даты последних сообщений в контактах
            foreach ($this->recipients as $contact_id) {
                $this->model->updateContactsDateLastMsg($this->sender_id, $contact_id);
            }

            cmsEventsManager::hook('send_user_message', [$this->sender_id, $this->recipients, $content]);
        }

        return $message_id ? $message_id : false;
    }

    /**
     * Устанавливает флаг игнорирования опций уведомлений пользователя
     *
     * @return \messages
     */
    public function ignoreNotifyOptions() {

        $this->is_ignore_options = true;

        return $this;
    }

    /**
     * Отправляет уведомление через личные сообщения
     *
     * @param array $notice
     * @param string $notice_type
     * @return int | false
     */
    public function sendNoticePM($notice, $notice_type = false) {

        if (!$notice_type) {

            $recipients = $this->recipients;

        } else {

            $options_only = $this->is_ignore_options ? false : ['pm', 'both'];

            $recipients = $this->model_users->getNotifiedUsers($notice_type, $this->recipients, $options_only);

            $this->is_ignore_options = false;
        }

        if (!$recipients) {
            return false;
        }

        list($recipients, $notice, $notice_type) = cmsEventsManager::hook('send_user_notice_before', [$recipients, $notice, $notice_type]);

        $notice_id = $this->model->addNotice($recipients, $notice);

        cmsEventsManager::hook('send_user_notice', [$recipients, $notice]);

        return $notice_id;
    }

    /**
     * Отправляет email-уведомления указанного типа всем
     * подписанным пользователям
     *
     * @param string $letter_name
     * @param string $notice Массив ключей и значений для замены в тексте письма
     * @param string $notice_type
     * @return boolean
     */
    public function sendNoticeEmail($letter_name, $notice = [], $notice_type = false) {

        if (!$this->recipients) {
            return;
        }

        $letter_text = cmsCore::getLanguageTextFile("letters/{$letter_name}");
        if (!$letter_text) {
            return false;
        }

        if (!$notice_type) {
            $notice_type = $letter_name;
        }

        $options_only = $this->is_ignore_options ? false : ['email', 'both'];

        $recipients = $this->model_users->getNotifiedUsers($notice_type, $this->recipients, $options_only);
        if (!$recipients) {
            return false;
        }

        $this->is_ignore_options = false;

        $letter_text = string_replace_keys_values($letter_text, $notice);

        list(
            $recipients,
            $letter_name,
            $notice,
            $notice_type,
            $letter_text
        ) = cmsEventsManager::hook('messages_send_notice_email', [
            $recipients,
            $letter_name,
            $notice,
            $notice_type,
            $letter_text
        ]);

        foreach ($recipients as $recipient) {

            $to = [
                'name'  => $recipient['nickname'],
                'email' => $recipient['email']
            ];

            $letter = [
                'text' => string_replace_keys_values($letter_text, $recipient)
            ];

            $this->sendEmail($to, $letter);
        }

        return true;
    }

    /**
     * Подготоваливает Email сообщение
     * Ставит в очередь или сразу отправляет
     *
     * @param array|string $to       Кому отправлять сообщение
     * @param array|string $letter   Текст письма
     * @param array $data            Массив данных, которыми нужно заменить выражения в письме
     * @param boolean $is_nl2br_text Расставлять переносы тегами <br>
     *
     * @return boolean
     */
    public function sendEmail($to, $letter, $data = [], $is_nl2br_text = true) {

        if (!is_array($to)) {
            $to = ['email' => $to];
        }

        $to = array_merge([
            'email'          => false,
            'name'           => false,
            'email_reply_to' => false,
            'name_reply_to'  => false,
            'custom_headers' => []
        ], $to);

        if (empty($to['email'])) {
            return false;
        }

        if (is_array($letter)) {
            if (empty($letter['text'])) {
                $letter['text'] = cmsCore::getLanguageTextFile("letters/{$letter['name']}");
            }
        } else {
            $letter = ['text' => cmsCore::getLanguageTextFile("letters/{$letter}")];
        }

        if (!$letter['text']) {
            return false;
        }

        $data = array_merge([
            'site' => $this->cms_config->sitename,
            'ip'   => cmsUser::getIp(),
            'date' => html_date(),
            'time' => html_time()
        ], $data);

        list(
            $to,
            $letter,
            $data,
            $is_nl2br_text
        ) = cmsEventsManager::hook('before_send_email_prepare', [
            $to,
            $letter,
            $data,
            $is_nl2br_text
        ]);

        $letter['text'] = string_replace_keys_values_extended($letter['text'], $data);

        $before_send = cmsEventsManager::hook('before_send_email', [
            'send_email' => true,
            'success'    => false,
            'to'         => $to,
            'letter'     => $letter
        ]);

        if (!$before_send['send_email']) {
            return $before_send['success'];
        }

        // если используем очередь
        if (!empty($this->options['use_queue'])) {

            cmsQueue::pushOn('email', [
                'controller' => $this->name,
                'hook'       => 'queue_send_email',
                'params'     => [
                    $to, $letter, $is_nl2br_text
                ]
            ]);

            return true;
        }

        return $this->sendEmailRaw($to, $letter, $is_nl2br_text) === true ? true : false;
    }

    /**
     * Выполняет отправку email сообщения
     *
     * @param array|string $to       Кому отправлять сообщение
     * @param array|string $letter   Текст письма
     * @param boolean $is_nl2br_text Расставлять переносы тегами <br>
     *
     * @return boolean|string true или текст ошибки
     */
    public function sendEmailRaw($to, $letter, $is_nl2br_text = null) {

        // URL для отписки от рассылки
        $unsubscribe_url = $to['custom_headers']['List-Unsubscribe'] ?? $this->cms_config->host;

        if(empty($to['custom_headers']['List-Unsubscribe'])){
            $to['custom_headers']['List-Unsubscribe'] = $unsubscribe_url;
        }

        $mailer = new cmsMailer();

        list($letter, $is_nl2br_text, $to) = cmsEventsManager::hook('process_email_letter', [$letter, $is_nl2br_text, $to]);

        $mailer->addTo($to['email'], $to['name']);

        if (!empty($to['email_reply_to'])) {
            $mailer->setReplyTo($to['email_reply_to'], $to['name_reply_to']);
        }

        if (!empty($to['custom_headers'])) {
            foreach ($to['custom_headers'] as $name => $value) {
                $mailer->addCustomHeader($name, $value);
            }
        }

        if (!empty($to['attachments'])) {
            foreach ($to['attachments'] as $attach_name => $attach) {
                $mailer->addAttachment($attach, (is_numeric($attach_name) ? '' : $attach_name));
            }
        }

        $letter['text'] = $mailer->parseSubject($letter['text']);
        $letter['text'] = $mailer->parseAttachments($letter['text']);

        // Если есть общий шаблон писем
        if (!empty($this->options['email_template'])) {

            if($is_nl2br_text){

                $letter['text'] = nl2br($letter['text']);

                $is_nl2br_text = false;
            }

            $data_template = [
                'body'            => $letter['text'],
                'year'            => date('Y'),
                'site'            => $this->cms_config->sitename,
                'unsubscribe_url' => $unsubscribe_url
            ];

            $letter['text'] = string_replace_keys_values($this->options['email_template'], $data_template);
        }

        $mailer->setBodyHTML(($is_nl2br_text ? nl2br($letter['text']) : $letter['text']));

        $result = $mailer->send();

        $mailer->clearTo()->clearAttachments();

        return $result ? true : $mailer->getErrorInfo();
    }

}
