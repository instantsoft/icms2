<?php

class onUsersMenuUsers extends cmsAction {

    public function run($item){

        if (!$this->cms_user->is_logged) { return false; }

        $action = $item['action'];

        if ($action == 'profile'){

            return array(
                'url'   => href_to_profile($this->cms_user),
                'items' => false
            );

        }

        if ($action == 'settings'){

            return array(
                'url'   => href_to_profile($this->cms_user, array('edit')),
                'items' => false
            );

        }

        if ($action == 'subscribers' && $this->cms_user->subscribers_count){

            return array(
                'url'     => href_to_profile($this->cms_user, array('subscribers')),
                'counter' => $this->cms_user->subscribers_count
            );

        }

        if ($action == 'subscriptions'){

            $this->model->filterEqual('user_id', $this->cms_user->id);

            $subscriptions_count = $this->model->getCount('subscriptions_bind');

            $this->model->resetFilters();

            if (!$subscriptions_count) { return false; }

            return array(
                'url'     => href_to_profile($this->cms_user, array('subscriptions')),
                'counter' => $subscriptions_count
            );

        }

        return false;

    }

}
