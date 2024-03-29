<?php

class onSubscriptionsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name) {

        $this->model->filterEqual('user_id', $profile['id']);

        $this->count = $this->model->getCount('subscriptions_bind', 'id', true);

        if (!$this->count) {
            return false;
        }

        return [
            'counter' => $this->count
        ];
    }

}
