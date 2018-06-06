<?php

class onSubscriptionsPhotosToolbarHtml extends cmsAction {

    public function run($album){

        $ctype = $this->model_content->getContentTypeByName($album['ctype_name']);
        if(!$ctype){
            return '';
        }

        if(array_key_exists('enable_subscriptions', $ctype['options'])){
            if(!$ctype['options']['enable_subscriptions']){
                return '';
            }
        }

        if($album['user_id'] == $this->cms_user->id){
            return '';
        }

        $params = array(
            'field_filters' => array(),
            'filters' => array(array(
                'field'     => 'album_id',
                'condition' => 'eq',
                'value'     => (string)$album['id']
            ))
        );

        if(!empty($album['filter_values']['type'])){

            $params['filters'][] = array(
                'field'     => 'type',
                'condition' => 'eq',
                'value'     => (string)$album['filter_values']['type']
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
