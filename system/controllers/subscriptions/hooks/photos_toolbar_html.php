<?php

class onSubscriptionsPhotosToolbarHtml extends cmsAction {

    public function run($album){

        $params = array(
            'field_filters' => array(),
            'filters' => array(array(
                'field'     => 'album_id',
                'condition' => 'eq',
                'value'     => $album['id']
            ))
        );

        if(!empty($album['filter_values']['types'])){

            $params['filters'][] = array(
                'field'     => 'type',
                'condition' => 'eq',
                'value'     => $album['filter_values']['types']
            );

        }

        if(!empty($album['filter_values']['orientation'])){
            $params['filters'][] = array(
                'field'     => 'orientation',
                'condition' => 'eq',
                'value'     => $album['filter_values']['orientation']
            );
        }

        if(!empty($album['filter_values']['width'])){
            $params['filters'][] = array(
                'field'     => 'width',
                'condition' => 'ge',
                'value'     => (string)$album['filter_values']['width']
            );
        }

        if(!empty($album['filter_values']['height'])){
            $params['filters'][] = array(
                'field'     => 'height',
                'condition' => 'ge',
                'value'     => (string)$album['filter_values']['height']
            );
        }

        return $this->renderSubscribeButton(array(
            'controller' => 'photos',
            'subject'    => 'album',
            'params'     => $params
        ));

    }

}
