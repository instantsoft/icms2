<?php

class actionMessagesRefresh extends cmsAction {

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
        $last_date  = $this->request->get('last_date');

        $contact = $this->model->getContact($this->cms_user->id, $contact_id);
        if (!$contact) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $messages = $this->model->filterEqual('is_new', 1)->getMessagesFromContact($this->cms_user->id, $contact_id);

        list($messages, $contact) = cmsEventsManager::hook('messages_before_list', [$messages, $contact]);

        if ($messages) {

            $messages_html = $this->cms_template->render('message', [
                'messages'  => $messages,
                'last_date' => $last_date,
                'is_notify' => true,
                'user'      => $this->cms_user
            ], new cmsRequest([], cmsRequest::CTX_INTERNAL));

            $this->model->setMessagesReaded($this->cms_user->id, $contact_id);
        }

        return $this->cms_template->renderJSON([
            'error'         => false,
            'contact_id'    => $contact['contact_id'],
            'is_online'     => (int) $contact['is_online'],
            'log_date_text' => ($contact['is_online'] ? LANG_ONLINE : string_date_age_max($contact['date_log'], true)),
            'date_log'      => mb_strtolower(string_date_age_max($contact['date_log'], true)),
            'html'          => ($messages ? $messages_html : false)
        ]);
    }

}
