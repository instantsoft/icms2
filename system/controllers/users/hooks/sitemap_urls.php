<?php

class onUsersSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $users = $this->model->
            filterIsNull('is_locked')->
            filterIsNull('is_deleted')->
            limit(false)->getUsersIds();

        if ($users){
            foreach($users as $user_id => $user_is_online){
                $url = href_to_abs($this->name, $user_id);
                $date_last_modified = false;
                $urls[$url] = $date_last_modified;
            }
        }

        return $urls;

    }

}
