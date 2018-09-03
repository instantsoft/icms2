<?php

class onUsersSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $this->model->filterIsNull('is_locked')->
            filterIsNull('is_deleted')->
            limit(false)->
            selectOnly('i.id', 'id')->
            select('i.nickname', 'nickname')->
            select('i.date_log', 'date_log');

        $users = $this->model->get('{users}');

        if ($users){
            foreach($users as $user){
                $urls[] = array(
                    'last_modified' => $user['date_log'],
                    'title'         => $user['nickname'],
                    'url'           => href_to_abs($this->name, $user['id'])
                );
            }
        }

        return $urls;

    }

}
