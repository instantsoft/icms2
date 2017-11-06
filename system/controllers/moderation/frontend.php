<?php
class moderation extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Ставит запись на модерацию и отправляет уведомления модератору
     *
     * @param string $target_name Имя цели модерации
     * @param array $item Массив модерируемой записи
     * @param boolean $is_new_item Новая запись или редактируемая
     * @return void
     */
    public function requestModeration($target_name, $item, $is_new_item = true){

        $moderator_id = $this->model->getNextModeratorId($target_name);

        $users_model = cmsCore::getModel('users');
        $messenger = cmsCore::getController('messages');

        $moderator = $users_model->getUser($moderator_id);
        $author = $users_model->getUser($item['user_id']);
        if(!$author){ return; }

        // добавляем задачу модератору
        $this->model->addModeratorTask($target_name, $moderator_id, $is_new_item, $item);

        // личное сообщение
        if($moderator['is_online']){
            $messenger->addRecipient($moderator['id'])->sendNoticePM(array(
                'content' => LANG_MODERATION_NOTIFY,
                'actions' => array(
                    'view' => array(
                        'title' => LANG_SHOW,
                        'href'  => $item['page_url']
                    )
                )
            ));
        }

        // EMAIL уведомление, если не онлайн
        if(!$moderator['is_online']){

            $to = array('email' => $moderator['email'], 'name' => $moderator['nickname']);

            $messenger->sendEmail($to, 'moderation', array(
                'moderator'  => $moderator['nickname'],
                'author'     => $author['nickname'],
                'author_url' => href_to_abs('users', $author['id']),
                'page_title' => $item['title'],
                'page_url'   => $item['page_url'],
                'date'       => html_date_time()
            ));

        }

        cmsUser::addSessionMessage(sprintf(LANG_MODERATION_IDLE, $moderator['nickname']), 'info');

        return;

    }

    /**
     * Отправляет автору уведомление о модерации
     * успешной или неуспешной
     *
     * @param array $item Массив модерируемой записи
     * @param string $letter moderation_approved или moderation_refused
     * @return $this
     */
    public function moderationNotifyAuthor($item, $letter){

        $users_model = cmsCore::getModel('users');
        $messenger = cmsCore::getController('messages');

        $author = $users_model->getUser($item['user_id']);
        if(!$author){ return $this; }

        if($author['is_online']){
            $messenger->addRecipient($author['id'])->sendNoticePM(array(
                'content' => sprintf(string_lang('PM_'.$letter), $item['title'], (isset($item['page_url']) ? $item['page_url'] : ''), (isset($item['reason']) ? $item['reason'] : ''))
            ));
        }

        if(!$author['is_online']){

            $to = array('email' => $author['email'], 'name' => $author['nickname']);

            $messenger->sendEmail($to, $letter, array(
                'nickname'   => $author['nickname'],
                'page_title' => $item['title'],
                'page_url'   => (isset($item['page_url']) ? $item['page_url'] : ''),
                'reason'     => (isset($item['reason']) ? $item['reason'] : ''),
                'date'       => html_date_time()
            ));

        }

        return $this;

    }

}
