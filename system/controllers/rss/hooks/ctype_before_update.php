<?php

class onRssCtypeBeforeUpdate extends cmsAction {

    public function run($ctype){

        $feed = $this->model->getFeedByCtypeName($ctype['name']);

        if(!$feed){

            $this->model->addFeed(array(
                'ctype_name'  => $ctype['name'],
                'title'       => $ctype['title'],
                'description' => $ctype['description'],
                'mapping'     => array(
                    'title'       => 'title',
                    'description' => 'content',
                    'pubDate'     => 'date_pub',
                    'image'       => '',
                    'image_size'  => 'normal'
                ),
                'is_enabled'  => !empty($ctype['options']['is_rss'])
            ));

        } else {

            $this->model->updateFeed($feed['id'], array(
                'is_enabled' => !empty($ctype['options']['is_rss']),
                'title' => $ctype['title']
            ));

        }

        return $ctype;

    }

}
