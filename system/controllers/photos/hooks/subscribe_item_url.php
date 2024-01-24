<?php

class onPhotosSubscribeItemUrl extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subscription) {

        $url    = href_to_rel($this->name);
        $params = [];

        if (!empty($subscription['params']['filters'])) {

            $filter_panel = [
                'type'        => (!empty($this->options['types']) ? (['' => LANG_PHOTOS_ALL] + $this->options['types']) : []),
                'orientation' => modelPhotos::getOrientationList(),
                'width'       => '',
                'height'      => ''
            ];

            foreach ($subscription['params']['filters'] as $filters) {

                if (is_array($filters['value'])) {
                    continue;
                }

                if($filters['field'] === 'album_id') {

                    $album = $this->model->getAlbum((int)$filters['value']);

                    if (!$album) {
                        return false;
                    }

                    $url = href_to_rel($album['ctype']['name'], $album['slug'] . '.html');

                    continue;
                }

                if (!isset($filter_panel[$filters['field']])) {
                    continue;
                }

                if (is_array($filter_panel[$filters['field']]) && isset($filter_panel[$filters['field']][$filters['value']])) {
                    $params[$filters['field']] = $filters['value'];
                }

                if (is_string($filter_panel[$filters['field']]) && is_numeric($filters['value'])) {
                    $params[$filters['field']] = $filters['value'];
                }
            }

            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
        }

        return $url;
    }

}
