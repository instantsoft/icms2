<?php

class onRssCtypeAfterDelete extends cmsAction {

    public function run($ctype){

        $feed = $this->model->getFeedByCtypeId($ctype['id']);
		
        if (!$feed){ return $ctype; }

        $this->model->deleteFeed($feed['id']);

        return $ctype;

    }

}
