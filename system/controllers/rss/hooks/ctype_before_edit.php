<?php

class onRssCtypeBeforeEdit extends cmsAction {

    public function run($ctype){

        $feed = $this->model->getFeedByCtypeName($ctype['name']);

        if ($feed) {
            $ctype['options']['is_rss'] = $feed['is_enabled'];
        }

        return $ctype;

    }

}
