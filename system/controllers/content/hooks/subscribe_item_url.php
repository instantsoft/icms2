<?php

class onContentSubscribeItemUrl extends cmsAction {

    public function run($subscription){

        $ctype = $this->model->getContentTypeByName($subscription['subject']);
        if(!$ctype){
            return false;
        }

        $url = href_to($ctype['name']); $params = array(); $ds = array();

        if(empty($subscription['params']['filters']) && empty($subscription['params']['field_filters'])){
            return $url;
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        // набор
        if(!empty($subscription['params']['dataset']['id'])){

            $ds = $this->model->getContentDataset($subscription['params']['dataset']['id']);

            if($ds){
                $url .= '-'.$ds['name'];
            }

        }

        if(!empty($subscription['params']['filters'])){

            // категория
            if($subscription['params']['filters'][0]['field'] == 'category_id'){

                $cat = $this->model->getCategory($ctype['name'], $subscription['params']['filters'][0]['value']);

                if($cat){

                    $url .= '/'.$cat['slug'];

                    unset($subscription['params']['filters'][0]);

                }

            }

            foreach ($subscription['params']['filters'] as $filters) {
                if(isset($fields[$filters['field']])){
                    $params[$filters['field']] = $filters['value'];
                }
            }

        }

        if(!empty($subscription['params']['field_filters'])){

            foreach ($subscription['params']['field_filters'] as $field_name => $field_value) {
                if(isset($fields[$field_name])){
                    $params[$field_name] = $field_value;
                }
            }

        }

        if($ds && !empty($subscription['params']['dataset']['fields'])){
            foreach ($subscription['params']['dataset']['fields'] as $ds_field_name) {
                if(isset($params[$ds_field_name])){
                    unset($params[$ds_field_name]);
                }
            }
        }

        if(!empty($params)){
            $url .= '?'.http_build_query($params);
        }

        return $url;

    }

}
