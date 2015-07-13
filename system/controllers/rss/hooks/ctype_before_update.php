<?php

class onRssCtypeBeforeUpdate extends cmsAction {

    public function run($ctype){

        $feed = $this->model->getFeedByCtypeName($ctype['name']);

        if ($feed) {
            $this->model->updateFeed($feed['id'], array(
                'is_enabled' => $ctype['options']['is_rss'],
                'title' => $ctype['title']
            ));
        }

        return $ctype;

    }

}
