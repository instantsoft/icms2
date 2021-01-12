<?php

class onPhotosSubscriptionMatchList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subscription, $items) {

        $params     = [];
        $match_list = [];

        if (!empty($subscription['params']['filters'])) {
            foreach ($subscription['params']['filters'] as $filters) {
                $params[$filters['field']] = $filters['value'];
            }
        }

        // проверяем фотографии по этому списку
        foreach ($items as $photo) {

            $is_coincides = false;

            if ($params) {

                $found = [];

                // проверяем фильтрацию
                foreach ($params as $key => $value) {

                    // для ширины и высоты отдельные фильтры
                    if (in_array($key, array('width', 'height'))) {
                        if ($photo[$key] >= $value) {
                            $found[] = $key;
                        }
                    } else {
                        if ($photo[$key] == $value) {
                            $found[] = $key;
                        }
                    }
                }

                // все фильтры должны совпасть
                if (count($found) == count($params)) {
                    $is_coincides = true;
                }
            } else {
                $is_coincides = true;
            }

            if ($is_coincides) {

                $_presets     = array_keys($photo['image']);
                $small_preset = end($_presets);

                $match_list[] = [
                    'url'       => href_to_abs('photos', $photo['slug'] . '.html'),
                    'image_src' => html_image_src($photo['image'], $small_preset, true, false),
                    'title'     => $photo['title']
                ];
            }
        }

        return $match_list;
    }

}
