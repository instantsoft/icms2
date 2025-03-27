<?php
/**
 * @property \messages $controller_messages
 */
class onCommentsUserLogin extends cmsAction {

    public function run($user) {

        // Если пользователь отключил уведомления о новых комментариях
        // через личные сообщения, то выходим
        if (empty($user['notify_options']['comments_new']) ||
            !in_array($user['notify_options']['comments_new'], ['pm', 'both'])) {
            return $user;
        }

        // Если новых комментариев на отслеживаемых страницах не появлялось
        // то тоже выходим
        $counts = $this->model->getTrackedNewCounts($user['id'], $user['date_log']);
        if (!$counts) {
            return $user;
        }

        $this->controller_messages->addRecipient($user['id']);

        foreach ($counts as $data) {

            $spellcount = html_spellcount($data['count'], LANG_NEW_COMMENT1, LANG_NEW_COMMENT2, LANG_NEW_COMMENT10);

            $notice = [
                'content' => sprintf(LANG_COMMENTS_TRACKED_NEW, $data['target_title'], $spellcount),
                'actions' => [
                    'view' => [
                        'title' => LANG_SHOW,
                        'href'  => href_to($data['target_url']) . '?new_comments#comments'
                    ],
                    'stop' => [
                        'title'      => LANG_COMMENTS_TRACK_STOP,
                        'controller' => $this->name,
                        'action'     => 'track_stop',
                        'params'     => [$data['target_controller'], $data['target_subject'], $data['target_id']]
                    ]
                ]
            ];

            $this->controller_messages->sendNoticePM($notice, 'comments_new');
        }

        return $user;
    }

}
