<?php

class actionMessagesSend extends cmsAction {

    /**
     * @var array Описание правил валидации входных данных
     */
    public $request_params = [
        'contact_id' => [
            'default' => 0,
            'rules'   => [
                ['required'],
                ['digits']
            ]
        ],
        'content'    => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['max_length', 65535]
            ]
        ],
        'csrf_token' => [
            'default' => '',
            'rules'   => [
                ['required']
            ]
        ],
        'last_date'  => [
            'default' => '',
            'rules'   => [
                ['regexp', "/^([a-z0-9 ]*)$/ui"]
            ]
        ]
    ];

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->isInGroups($this->options['groups_allowed'])) {
            return cmsCore::error404();
        }

        $contact_id = $this->request->get('contact_id');
        $content    = $this->request->get('content');
        $last_date  = $this->request->get('last_date');

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token'))) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => ''
            ]);
        }

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);

        // Контакт существует?
        if (!$contact) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => ''
            ]);
        }

        // Контакт не в игноре у отправителя?
        if ($contact['is_ignored']) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_PM_CONTACT_IS_IGNORED
            ]);
        }

        // Отправитель не в игноре у контакта?
        if ($this->model->isContactIgnored($contact_id, $this->cms_user->id)) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_PM_YOU_ARE_IGNORED
            ]);
        }

        // Есть ли уже сообщения в диалоге
        $messages = $this->model->limit(1)->getMessages($this->cms_user->id, $contact_id);

        // Контакт принимает сообщения от этого пользователя?
        if (!$this->cms_user->isPrivacyAllowed($contact, 'messages_pm') && !$messages) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_PM_CONTACT_IS_PRIVATE
            ]);
        }

        //
        // Отправляем сообщение
        //

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', [
            'text'         => $content,
            'typograph_id' => ($this->options['typograph_id'] ?? 1),
            'is_auto_br'   => !$editor_params['editor'] ? true : null
        ]);

        // Если редактор не указан, то это textarea, вырезаем все теги
        if(!$editor_params['editor']){
            $content_html = trim(strip_tags($content_html, '<br>'));
        }

        if (!$content_html) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'date'    => false,
                'message' => LANG_PM_SEND_ERROR
            ]);
        }

        if(mb_strlen($content_html) > 65535){
            return $this->cms_template->renderJSON([
                'error'   => true,
                'date'    => false,
                'message' => sprintf(ERR_VALIDATE_MAX_LENGTH, 65535)
            ]);
        }

        $this->setSender($this->cms_user->id)->addRecipient($contact_id);

        $message_id = $this->sendMessage($content_html);

        //
        // Отправляем уведомление на почту
        //
        $user_to = cmsCore::getModel('users')->getUser($contact_id);

        if (!$user_to['is_online']) {
            if($this->model->getNewMessagesCount($user_to['id']) == 1){
                $this->sendNoticeEmail('messages_new', [
                    'user_url'      => href_to_profile($this->cms_user, false, true),
                    'user_nickname' => $this->cms_user->nickname,
                    'message'       => strip_tags($content_html)
                ]);
            }
        }

        //
        // Получаем и рендерим добавленное сообщение
        //
        $message = $this->model->getMessage($message_id);

        $message_html = $this->cms_template->render('message', [
            'messages'  => [$message],
            'last_date' => $last_date,
            'user'      => $this->cms_user
        ], new cmsRequest([], cmsRequest::CTX_INTERNAL));

        return $this->cms_template->renderJSON([
            'error'   => false,
            'date'    => date($this->cms_config->date_format, time()),
            'message' => $message_html
        ]);
    }

}
