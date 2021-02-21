<?php

class onPhotosSubscribeListTitle extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target, $subscribe) {

        $result_title = LANG_PHOTOS;
        $titles       = [];

        if (!empty($target['params']['filters'])) {

            $filter_panel = array(
                'type'        => (!empty($this->options['types']) ? (['' => LANG_PHOTOS_ALL] + $this->options['types']) : []),
                'orientation' => modelPhotos::getOrientationList(),
                'width'       => LANG_PHOTOS_MORE_THAN . ' %s px ' . LANG_PHOTOS_BYWIDTH,
                'height'      => LANG_PHOTOS_MORE_THAN . ' %s px ' . LANG_PHOTOS_BYHEIGHT
            );

            // альбом
            if ($target['params']['filters'][0]['field'] == 'album_id') {

                $album = $this->model->getAlbum($target['params']['filters'][0]['value']);

                if ($album) {

                    $titles[] = $album['title'];

                    unset($target['params']['filters'][0]);
                }
            }

            foreach ($target['params']['filters'] as $filters) {
                if (is_array($filter_panel[$filters['field']]) && isset($filter_panel[$filters['field']][$filters['value']])) {
                    $titles[] = $filter_panel[$filters['field']][$filters['value']];
                }
                if (is_string($filter_panel[$filters['field']]) && is_numeric($filters['value'])) {
                    $titles[] = sprintf($filter_panel[$filters['field']], $filters['value']);
                }
            }

            if (!empty($titles)) {
                $result_title .= ' — ' . mb_strtolower(implode(', ', $titles));
            }
        }

        return $result_title;
    }

}
