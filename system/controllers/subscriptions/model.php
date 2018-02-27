<?php

class modelSubscriptions extends cmsModel {

    public function deleteUserSubscriptions($user_id) {

        return $this->filterEqual('user_id', $user_id)->deleteFiltered('subscriptions_bind');

    }

    public function getSubscriptionItem($hash) {

        return $this->filterEqual('hash', $hash)->getItem('subscriptions');

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

}
