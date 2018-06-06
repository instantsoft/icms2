<?php

class onSubscriptionsContentPhotosAfterAdd extends cmsAction {

    public function run($data){

        list($photos, $album, $ctype) = $data;

        if(!empty($album['is_private'])){
            return $data;
        }

        /**
         * Т.к. объём может быть большой сразу создаём задачу,
         * в которой и будем всё делать
         * Списки соответствия формирует хук make_subscription_match_list исполняющего контроллера
         * Создание очереди => формирование списка соответствия => выборка подписчиков => рассылка уведомлений
         */
        cmsQueue::pushOn('subscriptions', array(
            'controller' => $this->name,
            'hook'       => 'send_letters',
            'params'     => array(
                'photos', 'album', $photos
            )
        ));

        return $data;

    }

}
