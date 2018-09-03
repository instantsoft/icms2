<?php

class onPhotosSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'photo') { return $urls; }

        $photos = $this->model->limit(false)->getPhotos(0, false, array('title', 'slug'));

        if ($photos){
            foreach($photos as $photo){
                $urls[] = array(
                    'last_modified' => null,
                    'title'         => $photo['title'],
                    'url'           => href_to_abs($this->name, $photo['slug'].'.html')
                );
            }
        }

        return $urls;

    }

}
