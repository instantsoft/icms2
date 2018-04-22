<?php

class onContentSubscribeListTitle extends cmsAction {

    public function run($target, $subscribe){

        $ctype = $this->model->getContentTypeByName($target['subject']);
        if(!$ctype){
            return false;
        }

        $result_title = $ctype['title']; $titles = array();

        // нет фильтров
        if(empty($target['params']['filters']) && empty($target['params']['field_filters'])){
            return $result_title;
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        if(!empty($target['params']['filters'])){

            foreach ($target['params']['filters'] as $key => $filters) {

                // пользователь
                if($filters['field'] == 'user_id'){

                    $user = $this->model_users->getUser($filters['value']);

                    if($user){
                        $titles[] = $user['nickname'];
                    }

                    continue;

                }
                // папка
                if($filters['field'] == 'folder_id'){

                    $folder = $this->model->getContentFolder($filters['value']);

                    if($folder){
                        $titles[] = mb_strtolower($folder['title']);
                    }

                    continue;

                }
                // группа
                if($filters['field'] == 'parent_id' && $target['params']['filters'][$key+1]['value'] == 'group'){

                    $group = $this->model_groups->getGroup($filters['value']);

                    if($group){
                        $titles[] = mb_strtolower($group['title']);
                    }

                    continue;

                }
                // связь
                if($filters['field'] == 'relation'){

                    $item = $this->model->getContentItem($filters['value']['parent_ctype_id'], $filters['value']['parent_item_id']);

                    if($item){

                        $titles[] = mb_strtolower($item['title']);

                        $child_ctype = $this->model->getContentType($filters['value']['child_ctype_id']);

                        if($child_ctype){
                            $titles[] = mb_strtolower($child_ctype['title']);
                        }

                    }

                    continue;

                }
                // категория
                if($filters['field'] == 'category_id'){

                    $cat = $this->model->getCategory($ctype['name'], $filters['value']);

                    if($cat){
                        $titles[] = mb_strtolower($cat['title']);
                    }

                    continue;

                }

                if(isset($fields[$filters['field']])){

                    $result = $fields[$filters['field']]['handler']->getStringValue($filters['value']);

                    if($result){
                        $titles[] = mb_strtolower($fields[$filters['field']]['title'].': '.$result);
                    }

                }
            }

        }

        if(!empty($target['params']['field_filters'])){

            foreach ($target['params']['field_filters'] as $field_name => $field_value) {
                if(isset($fields[$field_name])){

                    if($fields[$field_name]['handler']->getDefaultVarType(true) !== 'array' && is_array($field_value)){
                        foreach ($field_value as $field_val) {

                            $result = $fields[$field_name]['handler']->getStringValue($field_val);

                            if($result){
                                $titles[] = mb_strtolower($fields[$field_name]['title'].': '.$result);
                            }

                        }
                    } else {

                        $result = $fields[$field_name]['handler']->getStringValue($field_value);

                        if($result){
                            $titles[] = mb_strtolower($fields[$field_name]['title'].': '.$result);
                        }

                    }

                }
            }

        }

        if(!empty($titles)){
            $result_title .= ' — '.implode(', ', array_unique($titles));
        }

        return $result_title;

    }

}
