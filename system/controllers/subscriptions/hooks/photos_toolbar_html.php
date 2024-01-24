<?php

class onSubscriptionsPhotosToolbarHtml extends cmsAction {

    public function run($album) {

        $ctype = $this->model_content->getContentTypeByName($album['ctype_name']);
        if (!$ctype) {
            return '';
        }

        if (array_key_exists('enable_subscriptions', $ctype['options'])) {
            if (!$ctype['options']['enable_subscriptions']) {
                return '';
            }
        }

        if ($album['user_id'] == $this->cms_user->id) {
            return '';
        }

        $params = [
            'field_filters' => [],
            'filters'       => [
                [
                    'field'     => 'album_id',
                    'condition' => 'eq',
                    'value'     => (string) $album['id']
                ]
            ]
        ];

        if (!empty($album['filter_values']['type'])) {

            $params['filters'][] = [
                'field'     => 'type',
                'condition' => 'eq',
                'value'     => (string) $album['filter_values']['type']
            ];
        }

        if (!empty($album['filter_values']['orientation'])) {

            $params['filters'][] = [
                'field'     => 'orientation',
                'condition' => 'eq',
                'value'     => $album['filter_values']['orientation']
            ];
        }

        if (!empty($album['filter_values']['width'])) {

            $params['filters'][] = [
                'field'     => 'width',
                'condition' => 'ge',
                'value'     => (string) $album['filter_values']['width']
            ];
        }

        if (!empty($album['filter_values']['height'])) {

            $params['filters'][] = [
                'field'     => 'height',
                'condition' => 'ge',
                'value'     => (string) $album['filter_values']['height']
            ];
        }

        return $this->renderSubscribeButton([
            'controller' => 'photos',
            'subject'    => 'album',
            'params'     => $params
        ]);
    }

}
