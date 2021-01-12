<?php

class onContentSubscribeItemUrl extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subscription){

        $ctype = $this->model->getContentTypeByName($subscription['subject']);
        if(!$ctype){
            return false;
        }

        $url = href_to_rel($ctype['name']); $params = array(); $ds = array(); $ds_prefix = '-'; $is_cat_ds = false;

        if(empty($subscription['params']['filters']) && empty($subscription['params']['field_filters'])){
            return $url;
        }

        // id категории для свойств
        $category_id = 0;

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        // набор
        if(!empty($subscription['params']['dataset']['id'])){

            $ds = $this->model->getContentDataset($subscription['params']['dataset']['id']);

        }

        if(!empty($subscription['params']['filters'])){

            foreach ($subscription['params']['filters'] as $key => $filters) {

                // пользователь
                if($filters['field'] == 'user_id'){

                    $user = $this->model_users->getUser($filters['value']);

                    if($user){
                        $url = href_to_rel('users', (empty($user['slug']) ? $user['id'] : $user['slug']), array('content', $ctype['name']));
                        $ds_prefix = '/';

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
                if($filters['field'] == 'parent_id' && $subscription['params']['filters'][$key+1]['value'] == 'group'){

                    $group = $this->model_groups->getGroup($filters['value']);

                    if($group){

                        $url = href_to_rel('groups', $group['slug'], array('content', $ctype['name']));

                        $ds_prefix = '/';

                    }

                    continue;

                }
                // связь
                if($filters['field'] == 'relation'){

                    $item = $this->model->getContentItem($filters['value']['parent_ctype_id'], $filters['value']['parent_item_id']);

                    if($item){

                        $parent_ctype = $this->model->getContentType($filters['value']['parent_ctype_id']);

                        if($parent_ctype){

                            $child_ctype = $this->model->getContentType($filters['value']['child_ctype_id']);

                            if($child_ctype){

                                $url = href_to_rel($parent_ctype['name'], $item['slug'], array('view-'.$child_ctype['name']));

                                $ds_prefix = '/';

                            }

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

                            $url .= $ds_prefix.$ds['name'];

                        }

                        $url .= '/'.$cat['slug'];

                        $category_id = $cat['id'];

                    }

                    continue;

                }

                if(isset($fields[$filters['field']])){
                    $params[$filters['field']] = $filters['value'];
                }

            }

            if($ds && !$is_cat_ds){
                $url .= $ds_prefix.$ds['name'];
            }

        }

        // Получаем поля-свойства
        $props = $props_fields = false;
        if ($category_id > 1){
            $props = $this->model->getContentProps($ctype['name'], $category_id);
            if($props){
                $props_fields = $this->getPropsFields($props);
            }
        }

        if(!empty($subscription['params']['field_filters'])){

            foreach ($subscription['params']['field_filters'] as $field_name => $field_value) {

                $matches = array();

                // свойства или поля
                if(preg_match('/^p([0-9]+)$/i', $field_name, $matches)){

                    // нет свойств
                    if (!is_array($props)){
                        continue;
                    }

                    // нет такого свойства
                    if(!isset($props_fields[$matches[1]])){ continue; }

                } else {

                    // нет такого поля
                    if(!isset($fields[$field_name])){ continue; }

                }

                $params[$field_name] = $field_value;

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
