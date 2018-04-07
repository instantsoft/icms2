<?php

class onSubscriptionsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        $this->model->filterEqual('user_id', $profile['id']);

        $this->count = $this->model->getCount('subscriptions_bind');

        $this->model->resetFilters();

        if (!$this->count) { return false; }

        return array('counter' => $this->count);

    }

}
