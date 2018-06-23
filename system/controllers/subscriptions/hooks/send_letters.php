<?php

class onSubscriptionsSendLetters extends cmsAction {

	public function run($attempt, $controller_name, $subject, $items){

        // получаем список для контроллера и субъекта
        // где есть подписчики
        $subscriptions_list = $this->model->filterEqual('controller', $controller_name)->
                filterEqual('subject', $subject)->
                filterGt('subscribers_count', 0)->
                getSubscriptionsList();

        // нет списка, удаляем задачу, возвратив true
        if(!$subscriptions_list){
            return true;
        }

        $controller = cmsCore::getController($controller_name);

        // шаблон письма
        $letter_text = cmsCore::getLanguageTextFile('letters/subscribe_new_item');
        if (!$letter_text){ return false; }

        foreach ($subscriptions_list as $subscription) {

            // полный урл
            $list_url = rel_to_href($subscription['subject_url'], true);

            // получаем совпадения для подписки
            $match_list = $controller->runHook('subscription_match_list', array($subscription, $items), false);
            if(!$match_list){ continue; }

            // если получили совпадения, рассылаем уведомления
            // получаем подписчиков
            $subscribers = $this->model->getNotifiedUsers($subscription['id']);

            if(!$subscribers){
                continue;
            }

            list($subscription,
                    $subscribers,
                    $match_list) = cmsEventsManager::hook('notify_subscribers', array(
                        $subscription,
                        $subscribers,
                        $match_list
                    ));

            // ссылки на новые записи
            $links = array();

            foreach ($match_list as $m) {
                $links[] = '<a href="'.$m['url'].'">'.$m['title'].'</a>';
            }

            $links = implode(', ', $links);

            foreach ($subscribers as $user) {

                // уведомление
                if (in_array($user['notify_options']['subscriptions'], array('pm', 'both'))){

                    $this->model_messages->addNotice(array($user['id']), array(
                        'content' => sprintf(LANG_SBSCR_PM_NOTIFY, $list_url, $subscription['title'], $links)
                    ));

                }

                // email
                if (in_array($user['notify_options']['subscriptions'], array('email', 'both'))){

                    $unsubscribe_url = href_to_abs('subscriptions', 'email_unsubscribe', $user['confirm_token']);

                    $to = array(
                        'email'          => $user['email'],
                        'name'           => $user['nickname'],
                        'email_reply_to' => false,
                        'name_reply_to'  => false,
                        'custom_headers' => array(
                            'List-Unsubscribe' => $unsubscribe_url
                        )
                    );

                    $data = array(
                        'site'            => $this->cms_config->sitename,
                        'date'            => html_date(),
                        'time'            => html_time(),
                        'nickname'        => $user['nickname'],
                        'title'           => $subscription['title'],
                        'list_url'        => $list_url,
                        'unsubscribe_url' => $unsubscribe_url,
                        'subjects'        => $links
                    );

                    $letter = array(
                        'text' => string_replace_keys_values($letter_text, $data)
                    );

                    cmsQueue::pushOn('email', array(
                        'controller' => 'messages',
                        'hook'       => 'queue_send_email',
                        'params'     => array(
                            $to, $letter, true
                        )
                    ));

                }

            }

        }

        return true;

    }

}
