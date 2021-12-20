<?php

class onSubscriptionsSendLetters extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($attempt, $controller_name, $subject, $items) {

        // получаем список для контроллера и субъекта
        // где есть подписчики
        $subscriptions_list = $this->model->filterEqual('controller', $controller_name)->
                filterEqual('subject', $subject)->
                filterGt('subscribers_count', 0)->
                getSubscriptionsList();

        // нет списка, удаляем задачу, возвратив true
        if (!$subscriptions_list) {
            return true;
        }

        $controller = cmsCore::getController($controller_name);

        // Опции, если есть
        $subject_options = $controller->runHook('subscription_options', [$subject], []);

        // Шаблон письма
        // Если задан в опциях
        if(!empty($subject_options['letter_tpl'])){
            $letter_text = $subject_options['letter_tpl'];
        } else {
            $letter_text = cmsCore::getLanguageTextFile('letters/subscribe_new_item');
        }

        if (!$letter_text) {
            return false;
        }

        // Шаблон уведомления на сайте
        if(!empty($subject_options['notify_text'])){
            $notify_text = $subject_options['notify_text'];
        } else {
            $notify_text = LANG_SBSCR_PM_NOTIFY;
        }

        foreach ($subscriptions_list as $subscription) {

            // полный урл
            $list_url = rel_to_href($subscription['subject_url'], true);

            // получаем совпадения для подписки
            $match_list = $controller->runHook('subscription_match_list', [$subscription, $items], false);
            if (!$match_list) {
                continue;
            }

            // если получили совпадения, рассылаем уведомления
            // получаем подписчиков
            $subscribers = $this->model->getNotifiedUsers($subscription['id']);

            if (!$subscribers) {
                continue;
            }

            list($subscription,
                    $subscribers,
                    $match_list) = cmsEventsManager::hook('notify_subscribers', [
                        $subscription,
                        $subscribers,
                        $match_list
            ]);

            // ссылки на новые записи
            $links = [];

            foreach ($match_list as $m) {
                $links[] = '<a href="' . $m['url'] . '">' . $m['title'] . '</a>';
            }

            $links = implode(', ', $links);

            foreach ($subscribers as $user) {

                // уведомление
                if (in_array($user['notify_options']['subscriptions'], ['pm', 'both'])) {

                    $this->model_messages->addNotice([$user['id']], [
                        'content' => sprintf($notify_text, $list_url, $subscription['title'], $links)
                    ]);
                }

                // email
                if (in_array($user['notify_options']['subscriptions'], ['email', 'both'])) {

                    $unsubscribe_url = href_to_abs('subscriptions', 'email_unsubscribe', $user['confirm_token']);

                    $to = [
                        'email'          => $user['email'],
                        'name'           => $user['nickname'],
                        'email_reply_to' => false,
                        'name_reply_to'  => false,
                        'custom_headers' => [
                            'List-Unsubscribe' => $unsubscribe_url
                        ]
                    ];

                    $data = [
                        'site'            => $this->cms_config->sitename,
                        'date'            => html_date(),
                        'time'            => html_time(),
                        'nickname'        => $user['nickname'],
                        'title'           => $subscription['title'],
                        'list_url'        => $list_url,
                        'unsubscribe_url' => $unsubscribe_url,
                        'subjects'        => $links
                    ];

                    $letter = [
                        'text' => string_replace_keys_values($letter_text, $data)
                    ];

                    cmsQueue::pushOn('email', [
                        'controller' => 'messages',
                        'hook'       => 'queue_send_email',
                        'params'     => [
                            $to, $letter, true
                        ]
                    ]);
                }
            }
        }

        return true;
    }

}
