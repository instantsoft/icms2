<?php
/**
 * @property \modelModeration $model
 */
class moderation extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Ставит запись на модерацию и отправляет уведомления модератору
     *
     * @param string $target_name Имя цели модерации
     * @param array $item Массив модерируемой записи
     * @param boolean $is_new_item Новая запись или редактируемая
     * @param string $pm_message Текст сообщения для уведомления
     * @return mixed
     */
    public function requestModeration($target_name, $item, $is_new_item = true, $pm_message = LANG_MODERATION_NOTIFY) {

        $moderator_id = $this->model->getNextModeratorId($target_name);

        $moderator = $this->model_users->getUser($moderator_id);
        if (!$moderator) {
            return false;
        }

        list($moderator, $target_name, $item, $is_new_item, $pm_message) = cmsEventsManager::hook([
            'moderation_request_before',
            'moderation_' . $target_name . '_request_before'
        ], [$moderator, $target_name, $item, $is_new_item, $pm_message], null, $this->request);

        if ($item['user_id']) {
            $author = $this->model_users->getUser($item['user_id']);
            if (!$author) {
                return false;
            }
        } else {
            $author = [
                'nickname' => (!empty($item['author_name']) ? $item['author_name'] : LANG_GUEST)
            ];
        }

        // чтобы на рассылать множество уведомлений, проверяем, есть ли ожидающие модерации записи
        if (!$this->model->isModeratorTaskExists($target_name, $moderator['id'])) {

            // личное сообщение
            $this->controller_messages->addRecipient($moderator['id'])->sendNoticePM([
                'content' => $pm_message,
                'actions' => [
                    'view' => [
                        'title' => LANG_SHOW,
                        'href'  => $item['page_url']
                    ]
                ]
            ]);

            // EMAIL уведомление
            $to = ['email' => $moderator['email'], 'name' => $moderator['nickname']];

            $this->controller_messages->sendEmail($to, 'moderation', [
                'moderation_text' => $pm_message,
                'moderator'       => $moderator['nickname'],
                'author'          => $author['nickname'],
                'author_url'      => (!empty($author['id']) ? href_to_profile($author, false, true) : (!empty($item['author_email']) ? 'mailto:' . $item['author_email'] : '')),
                'page_title'      => $item['title'],
                'page_url'        => $item['page_url'],
                'date'            => html_date_time()
            ]);
        }

        // добавляем задачу модератору
        $this->model->addModeratorTask($target_name, $moderator['id'], $is_new_item, $item);

        return sprintf(LANG_MODERATION_IDLE, $moderator['nickname']);
    }

    /**
     * Отправляет автору уведомление о модерации
     * успешной или неуспешной
     *
     * @param array $item Массив модерируемой записи
     * @param string $letter moderation_approved || moderation_refused || moderation_rework
     * @return $this
     */
    public function moderationNotifyAuthor($item, $letter) {

        // автор гость
        if (empty($item['user_id'])) {

            // если не заданы параметры гостя
            if (empty($item['author_email'])) {
                return $this;
            }

            $author_name = (!empty($item['author_name']) ? $item['author_name'] : $item['author_email']);

            $to = ['email' => $item['author_email'], 'name' => $author_name];

            $this->controller_messages->sendEmail($to, $letter, [
                'nickname'   => $author_name,
                'page_title' => $item['title'],
                'page_url'   => (isset($item['page_url']) ? $item['page_url'] : ''),
                'reason'     => (isset($item['reason']) ? $item['reason'] : ''),
                'date'       => html_date_time()
            ]);

            return $this;
        }

        $author = $this->model_users->getUser($item['user_id']);
        if (!$author) {
            return $this;
        }

        // личное сообщение
        $this->controller_messages->addRecipient($author['id'])->sendNoticePM([
            'content' => sprintf(string_lang('PM_' . $letter), $item['title'], (isset($item['page_url']) ? $item['page_url'] : ''), (isset($item['reason']) ? $item['reason'] : ''))
        ]);

        // EMAIL уведомление
        $to = ['email' => $author['email'], 'name' => $author['nickname']];

        $this->controller_messages->sendEmail($to, $letter, [
            'nickname'   => $author['nickname'],
            'page_title' => $item['title'],
            'page_url'   => (isset($item['page_url']) ? $item['page_url'] : ''),
            'reason'     => (isset($item['reason']) ? $item['reason'] : ''),
            'date'       => html_date_time()
        ]);

        return $this;
    }

    /**
     * Одобряет модерацию для цели
     *
     * @param string $target_name Имя цели модерации
     * @param array $item Массив модерируемой записи
     * @param string $ups_key Ключ UPS для записей просмотра модераций
     * @param string $letter Название письма для уведомления
     * @return array $task
     */
    public function approve($target_name, $item, $ups_key = false, $letter = 'moderation_approved') {

        $task = $this->model->getModeratorTask($target_name, $item['id']);

        $this->model->closeModeratorTask($target_name, $item['id'], true, $this->cms_user->id);

        $after_action = $task['is_new_item'] ? 'add' : 'update';

        $data = cmsEventsManager::hook("content_after_{$after_action}_approve", ['ctype_name' => $target_name, 'item' => $item], null, $this->request);

        $item = $data['item'];

        $item = cmsEventsManager::hook("content_{$target_name}_after_{$after_action}_approve", $item, null, $this->request);

        $this->moderationNotifyAuthor($item, $letter);

        if ($ups_key) {
            cmsUser::deleteUPSlist($ups_key);
        }

        return $task;
    }

    /**
     * Отзывает запись с модерации и отправляет уведомления модератору
     *
     * @param string $target_name Имя цели модерации
     * @param array $item Массив модерируемой записи
     * @param string $ups_key Ключ UPS для записей просмотра модераций
     * @return boolean
     */
    public function cancelModeratorTask($target_name, $item, $ups_key = false) {

        $task = $this->model->cancelModeratorTask($target_name, $item['id'], $this->cms_user->id);
        if (!$task) {
            return false;
        }

        $moderator = $this->model_users->getUser($task['moderator_id']);
        if (!$moderator) {
            return false;
        }

        $author = $this->model_users->getUser($item['user_id']);
        if (!$author) {
            return false;
        }

        list($moderator, $target_name, $item, $task, $author) = cmsEventsManager::hook([
            'moderation_cancel',
            'moderation_' . $target_name . '_cancel'
        ], [$moderator, $target_name, $item, $task, $author], null, $this->request);

        // личное сообщение
        $this->controller_messages->addRecipient($moderator['id'])->sendNoticePM([
            'content' => LANG_MODERATION_RETURN_NOTIFY,
            'actions' => [
                'view' => [
                    'title' => LANG_SHOW,
                    'href'  => $item['page_url']
                ]
            ]
        ]);

        // EMAIL уведомление
        $to = ['email' => $moderator['email'], 'name' => $moderator['nickname']];

        $this->controller_messages->sendEmail($to, 'moderation_return', [
            'moderator'  => $moderator['nickname'],
            'author'     => $author['nickname'],
            'author_url' => href_to_profile($author, false, true),
            'page_title' => $item['title'],
            'page_url'   => $item['page_url']
        ]);

        if ($ups_key) {
            cmsUser::deleteUPSlist($ups_key);
        }

        return true;
    }

    /**
     * Отправляет запись на доработку
     *
     * @param string $target_name Имя цели модерации
     * @param array $item Массив модерируемой записи
     * @param string $ups_key Ключ UPS для записей просмотра модераций
     * @return boolean
     */
    public function reworkModeratorTask($target_name, $item, $ups_key = false) {

        $task = $this->model->cancelModeratorTask($target_name, $item['id'], $this->cms_user->id);
        if (!$task) {
            return false;
        }

        list($target_name, $item, $task) = cmsEventsManager::hook([
            'moderation_rework',
            'moderation_' . $target_name . '_rework'
        ], [$target_name, $item, $task], null, $this->request);

        $this->moderationNotifyAuthor($item, 'moderation_rework');

        if ($ups_key) {
            cmsUser::deleteUPSlist($ups_key);
        }

        return true;
    }

    /**
     * Возвращает массив кол-ва записей в черновиках,
     * разделённых по контроллерам
     *
     * @param integer $user_id
     * @return array
     */
    public function getUserDraftCounts($user_id) {

        $listeners = cmsEventsManager::getEventListeners('moderation_list');

        $counts = [];

        foreach ($listeners as $controller_name) {

            if (!cmsController::enabled($controller_name)) {
                continue;
            }

            $draft_counts = cmsCore::getModel($controller_name)->getDraftCounts($user_id);
            if (!$draft_counts) {
                continue;
            }

            if (is_numeric($draft_counts)) {
                $counts[$controller_name] = $draft_counts;
            } else {
                $counts = array_merge($counts, $draft_counts);
            }
        }

        return $counts;
    }

    /**
     * Проверяет, что пользователь модератор
     *
     * @param string $ctype_name
     * @param integer $user_id
     * @param array $item
     * @return boolean
     */
    public function userIsContentModerator($ctype_name, $user_id, $item = []) {

        if (!$user_id) {
            return false;
        }

        if ($this->cms_user->is_admin || $this->model->userIsContentModerator($ctype_name, $user_id)) {
            return true;
        }

        list($ctype_name, $user_id, $item, $is_moderator) = cmsEventsManager::hook([
            'moderation_user_is_moderator',
            'moderation_' . $ctype_name . '_user_is_moderator'
        ], [$ctype_name, $user_id, $item, false]);

        return $is_moderator;
    }

}
