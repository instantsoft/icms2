<?php

class onRssCtypeAfterAdd extends cmsAction {

    public function run($ctype){

        $feed = $this->model->getFeedByCtypeName($ctype['name']);

        if (!$feed) { return $ctype; }

        $this->model->updateFeed($feed['id'], array('ctype_id'=>$ctype['id']));
        
        return $ctype;

    }

}
