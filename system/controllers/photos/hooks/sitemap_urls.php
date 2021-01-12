<?php

class onPhotosSitemapUrls extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($type) {

        $urls = [];

        if ($type !== 'photo') {
            return $urls;
        }

        $photos = $this->model->limit(false)->getPhotos(0, false, ['title', 'slug']);

        if ($photos) {
            foreach ($photos as $photo) {
                $urls[] = [
                    'last_modified' => null,
                    'title'         => $photo['title'],
                    'url'           => href_to_abs($this->name, $photo['slug'] . '.html')
                ];
            }
        }

        return $urls;
    }

}
