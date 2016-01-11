<?php

class onContentRssContentControllerAfterUpdate extends cmsAction {

    public function run($feed){

        $ctype = $this->model->getContentType($feed['ctype_id']);

        $ctype['options']['is_rss'] = $feed['is_enabled'];

        $this->model->updateContentType($feed['ctype_id'], array(
            'options' => $ctype['options']
        ));

        return $feed;

    }

}