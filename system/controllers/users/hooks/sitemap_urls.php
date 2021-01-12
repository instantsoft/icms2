<?php

class onUsersSitemapUrls extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($type) {

        $urls = [];

        if ($type !== 'profiles') {
            return $urls;
        }

        $this->model->filterIsNull('is_locked')->
                filterIsNull('is_deleted')->
                limit(false)->
                selectOnly('i.id', 'id')->
                select('i.nickname', 'nickname')->
                select('i.slug', 'slug')->
                select('i.date_log', 'date_log');

        $users = $this->model->get('{users}');

        if ($users) {
            foreach ($users as $user) {
                $urls[] = [
                    'last_modified' => $user['date_log'],
                    'title'         => $user['nickname'],
                    'url'           => href_to_profile($user, false, true)
                ];
            }
        }

        return $urls;
    }

}
