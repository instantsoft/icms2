<?php

class onContentSubscribeItemUrl extends cmsAction {

    public function run($subscription){

        $ctype = $this->model->getContentTypeByName($subscription['subject']);
        if(!$ctype){
            return false;
        }

        $url = href_to_rel($ctype['name']); $params = array(); $ds = array(); $is_cat_ds = false;

        if(empty($subscription['params']['filters']) && empty($subscription['params']['field_filters'])){
            return $url;
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        // набор
        if(!empty($subscription['params']['dataset']['id'])){

            $ds = $this->model->getContentDataset($subscription['params']['dataset']['id']);

        }

        if(!empty($subscription['params']['filters'])){

            foreach ($subscription['params']['filters'] as $filters) {

                // пользователь
                if($filters['field'] == 'user_id'){

                    $user = $this->model_users->getUser($filters['value']);

                    if($user){
                        $url = href_to_rel('users', (empty($user['slug']) ? $user['id'] : $user['slug']), array('content', $ctype['name']));
                    }

                    continue;

                }
                // папка
                if($filters['field'] == 'folder_id'){

                    $folder = $this->model->getContentFolder($filters['value']);

                    if($folder){
                        $url .= '/'.$folder['id'];
                    }

                    continue;

                }
                // группа
                if($filters['field'] == 'parent_id' && $target['params']['filters'][$key+1]['value'] == 'group'){

                    $group = $this->model_groups->getGroup($filters['value']);

                    if($group){
                        $url = href_to_rel('groups', $group['slug'], array('content', $ctype['name']));
                    }

                    continue;

                }
                // связь
                if($filters['field'] == 'relation'){

                    $item = $this->model->getContentItem($filters['value']['parent_ctype_id'], $filters['value']['parent_item_id']);

                    if($item){

                        $child_ctype = $this->model->getContentType($filters['value']['child_ctype_id']);

                        if($child_ctype){
                            $url = href_to_rel($ctype['name'], $item['slug'], array('view-'.$child_ctype['name']));
                        }

                    }

                    continue;

                }
                // категория
                if($filters['field'] == 'category_id'){

                    $cat = $this->model->getCategory($ctype['name'], $filters['value']);

                    if($cat){

                        if($ds){

                            $is_cat_ds = true;

                            $url .= '-'.$ds['name'];

                        }

                        $url .= '/'.$cat['slug'];

                    }

                    continue;

                }

                if(isset($fields[$filters['field']])){
                    $params[$filters['field']] = $filters['value'];
                }

            }

            if($ds && !$is_cat_ds){
                $url .= '/'.$ds['name'];
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
