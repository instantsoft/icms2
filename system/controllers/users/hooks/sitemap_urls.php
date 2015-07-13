<?php

class onUsersSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $users = $this->model->
                            filterIsNull('is_locked')->
                            limit(false)->
                            getUsersIds();

        if ($users){
            foreach($users as $user){
                $url = href_to_abs($this->name, $user['id']);
                $date_last_modified = false;
                $urls[$url] = $date_last_modified;
            }
        }

        return $urls;

    }

}
