<?php

class onPhotosSubscribeListTitle extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target, $subscribe) {

        $result_title = LANG_PHOTOS;
        $titles       = [];

        if (!empty($target['params']['filters'])) {

            $is_params_set = [];

            $filter_panel = [
                'type'        => (!empty($this->options['types']) ? (['' => LANG_PHOTOS_ALL] + $this->options['types']) : []),
                'orientation' => modelPhotos::getOrientationList(),
                'width'       => LANG_PHOTOS_MORE_THAN . ' %s px ' . LANG_PHOTOS_BYWIDTH,
                'height'      => LANG_PHOTOS_MORE_THAN . ' %s px ' . LANG_PHOTOS_BYHEIGHT
            ];

            foreach ($target['params']['filters'] as $filters) {

                // В нормальных условиях массива быть не должно
                if (is_array($filters['value'])) {
                    return false;
                }

                // Один параметр - один раз
                if(!empty($is_params_set[$filters['field']])) {
                    return false;
                }
                $is_params_set[$filters['field']] = true;

                // Альбом
                if($filters['field'] === 'album_id') {

                    $album = $this->model->getAlbum((int)$filters['value']);

                    if (!$album) {
                        return false;
                    }

                    $titles[] = $album['title'];

                    continue;
                }

                // Только заданные параметры
                if (!isset($filter_panel[$filters['field']])) {
                    return false;
                }

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
