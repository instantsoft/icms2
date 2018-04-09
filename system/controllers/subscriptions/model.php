<?php

class modelSubscriptions extends cmsModel {

    public function verifySubscription($confirm_token) {

        return $this->filterEqual('confirm_token', $confirm_token)->
                updateFiltered('subscriptions_bind', array(
                    'is_confirmed'  => 1,
                    'confirm_token' => null
                ));

    }

    public function getSubscriptionByToken($confirm_token) {
        return $this->filterEqual('confirm_token', $confirm_token)->getItem('subscriptions_bind');
    }

    public function deleteUserSubscriptions($user_id) {

        return $this->filterEqual('user_id', $user_id)->deleteFiltered('subscriptions_bind');

    }

    public function getSubscriptionItem($hash_or_id) {

        if(is_numeric($hash_or_id)){
            $this->filterEqual('id', $hash_or_id);
        } else {
            $this->filterEqual('hash', $hash_or_id);
        }

        return $this->getItem('subscriptions', function ($item, $model){

            $item['params'] = cmsModel::stringToArray($item['params']);

            return $item;

        });

    }

    public function isSubscribed($target, $subscribe) {

        if(empty($target['hash'])){
            $target['hash'] = md5(serialize($target));
        }

        $list_item_id = $this->filterEqual('hash', $target['hash'])->getFieldFiltered('subscriptions', 'id');

        if(!$list_item_id){ return false; }

        if(!empty($subscribe['user_id'])){
            return $this->isUserSubscribed($subscribe['user_id'], $list_item_id);
        }

        return $this->isGuestSubscribed($subscribe['guest_email'], $list_item_id);

    }

    public function isUserSubscribed($user_id, $list_item_id) {

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('subscription_id', $list_item_id);

        return $this->getFieldFiltered('subscriptions_bind', 'id');

    }

    public function isGuestSubscribed($subscriber_email, $list_item_id) {

        $this->filterEqual('guest_email', $subscriber_email);
        $this->filterEqual('subscription_id', $list_item_id);

        return $this->getFieldFiltered('subscriptions_bind', 'id');

    }

    public function subscribe($target, $subscribe) {

        $is_now_create_list = false;

        if(empty($target['hash'])){
            $target['hash'] = md5(serialize($target));
        }

        // проверяем, нет ли такого списка
        $subscribe['subscription_id'] = $this->filterEqual('hash', $target['hash'])->getFieldFiltered('subscriptions', 'id');

        // создаём список
        if(!$subscribe['subscription_id']){

            $subscribe['subscription_id'] = $this->insert('subscriptions', $target, true);

            $is_now_create_list = $subscribe['subscription_id'];

        }

        $this->insert('subscriptions_bind', $subscribe);

        $this->reCountSubscribers($subscribe['subscription_id']);

        return $is_now_create_list;

    }

    public function unsubscribe($target, $subscribe) {

        if(empty($target['hash'])){
            $target['hash'] = md5(serialize($target));
        }

        $list_item_id = $this->filterEqual('hash', $target['hash'])->getFieldFiltered('subscriptions', 'id');

        if(!$list_item_id){
            return false;
        }

        if(!empty($subscribe['user_id'])){

            $this->filterEqual('user_id', $subscribe['user_id']);
            $this->filterEqual('subscription_id', $list_item_id);

            $this->deleteFiltered('subscriptions_bind', 'id');

        } else {

            $this->filterEqual('guest_email', $subscribe['guest_email']);
            $this->filterEqual('subscription_id', $list_item_id);

            $this->deleteFiltered('subscriptions_bind', 'id');

        }

        $this->reCountSubscribers($list_item_id);

        return true;

    }

    public function reCountSubscribers($subscription_id) {

        $this->db->query("UPDATE {#}subscriptions SET subscribers_count=(SELECT COUNT(id) FROM {#}subscriptions_bind WHERE subscription_id = '{$subscription_id}' AND is_confirmed = 1) WHERE id = '{$subscription_id}'");

        return $this;

    }

    public function getSubscriptions(){

        $this->joinUser()->joinSessionsOnline();

        $this->selectList(array(
            's.title'             => 'title',
            's.controller'        => 'controller',
            's.subject'           => 'subject',
            's.params'            => 'params',
            's.subscribers_count' => 'subscribers_count',
            's.hash'              => 'hash'
        ));

        $this->join('subscriptions', 's', 's.id = i.subscription_id');

        return $this->get('subscriptions_bind', function($item, $model) {

            $item['user'] = array(
                'id'         => $item['user_id'],
                'nickname'   => $item['user_nickname'],
                'is_online'  => $item['is_online'],
                'is_deleted' => $item['user_is_deleted'],
                'avatar'     => $item['user_avatar']
            );

            $item['params'] = cmsModel::stringToArray($item['params']);

            $item['target'] = array(
                'controller' => $item['controller'],
                'subject'    => $item['subject'],
                'params'     => $item['params']
            );

            return $item;

        }, false);

    }

}
