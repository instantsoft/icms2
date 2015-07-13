<?php

class onRssCtypeBeforeAdd extends cmsAction {

    public function run($ctype){

        $this->model->addFeed(array(
            'ctype_name' => $ctype['name'],
            'title' => $ctype['title'],
            'description' => $ctype['description'],
            'mapping' => array(
                'title' => 'title',
                'description' => 'content',
                'pubDate' => 'date_pub',
                'image' => '',
                'image_size' => 'normal'
            ),
            'is_enabled' => $ctype['options']['is_rss']
        ));

        return $ctype;

    }

}
