<?php

class onContentSubscribeListTitle extends cmsAction {

    public $disallow_event_db_register = true;

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

        // id категории для свойств
        $category_id = 0;

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        if(!empty($target['params']['filters'])){

            foreach ($target['params']['filters'] as $key => $filters) {

                // пользователь
                if($filters['field'] === 'user_id'){

                    $user = $this->model_users->getUser($filters['value']);

                    if($user){
                        $titles[] = $user['nickname'];
                    }

                    continue;
                }
                // папка
                if($filters['field'] === 'folder_id'){

                    $folder = $this->model->getContentFolder($filters['value']);

                    if($folder){
                        $titles[] = mb_strtolower($folder['title']);
                    }

                    continue;
                }
                // группа
                if($filters['field'] === 'parent_id' && $target['params']['filters'][$key+1]['value'] === 'group'){

                    $group = $this->model_groups->getGroup($filters['value']);

                    if($group){
                        $titles[] = mb_strtolower($group['title']);
                    }

                    continue;
                }
                // связь
                if($filters['field'] === 'relation'){

                    $item = $this->model->getContentItem($filters['value']['parent_ctype_id'], $filters['value']['parent_item_id']);

                    if($item){

                        // для связей стартовое название меняем на родительское
                        $parent_ctype = $this->model->getContentType($filters['value']['parent_ctype_id']);

                        if($parent_ctype){

                            $result_title = $parent_ctype['title'];

                            $titles[] = mb_strtolower($item['title']);

                            $child_ctype = $this->model->getContentType($filters['value']['child_ctype_id']);

                            if($child_ctype){
                                $titles[] = mb_strtolower($child_ctype['title']);
                            }

                        }

                    }

                    continue;
                }
                // категория
                if($filters['field'] === 'category_id'){

                    $cat = $this->model->getCategory($ctype['name'], $filters['value']);

                    if($cat){

                        $titles[] = mb_strtolower($cat['title']);

                        $category_id = $cat['id'];

                    }

                    continue;
                }

                if(isset($fields[$filters['field']])){

                    $result = '';

                    if(!empty($filters['condition'])){

                        switch($filters['condition']){

                            case 'gt': $result = '&gt; '.$filters['value']; break;
                            case 'lt': $result = '&lt; '.$filters['value']; break;
                            case 'ge': $result = '&ge; '.$filters['value']; break;
                            case 'le': $result = '&le; '.$filters['value']; break;
                            case 'nn': $result = LANG_FILTER_NOT_NULL; break;
                            case 'ni': $result = LANG_FILTER_IS_NULL; break;
                            case 'lk': $result = LANG_FILTER_LIKE.' '.$filters['value']; break;
                            case 'ln': $result = LANG_FILTER_NOT_LIKE.' '.$filters['value']; break;
                            case 'lb': $result = LANG_FILTER_LIKE_BEGIN.' '.$filters['value']; break;
                            case 'lf': $result = LANG_FILTER_LIKE_END.' '.$filters['value']; break;
                            case 'dy': $result = LANG_FILTER_DATE_YOUNGER.' '.$filters['value']; break;
                            case 'do': $result = LANG_FILTER_DATE_OLDER.' '.$filters['value']; break;

                        }

                    }

                    if(!$result){
                        $result = $fields[$filters['field']]['handler']->
                                setItem(['ctype_name' => $ctype['name'], 'ctype' => $ctype, 'id' => 0])->
                                getStringValue($filters['value']);
                    }

                    if($result){
                        $titles[] = mb_strtolower($fields[$filters['field']]['title'].' '.$result);
                    }

                }
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

        if(!empty($target['params']['field_filters'])){

            foreach ($target['params']['field_filters'] as $field_name => $field_value) {

                $matches = [];

                // свойства или поля
                if(preg_match('/^p([0-9]+)$/i', $field_name, $matches)){

                    // нет свойств
                    if (!is_array($props)){
                        continue;
                    }

                    // нет такого свойства
                    if(!isset($props_fields[$matches[1]])){ continue; }

                    $handler = $props_fields[$matches[1]];

                    $field_title = $props[$matches[1]]['title'];

                } else {

                    // нет такого поля
                    if(!isset($fields[$field_name])){ continue; }

                    $handler = $fields[$field_name]['handler'];

                    $field_title = $fields[$field_name]['title'];

                }

                $handler->setItem(['ctype_name' => $ctype['name'], 'ctype' => $ctype, 'id' => 0]);

                if($handler->getDefaultVarType(true) !== 'array' && is_array($field_value)){
                    foreach ($field_value as $field_val) {

                        $result = $handler->getStringValue($field_val);

                        if($result){
                            $titles[] = mb_strtolower($field_title.' '.$result);
                        }

                    }
                } else {

                    $result = $handler->getStringValue($field_value);

                    if($result){
                        $titles[] = mb_strtolower($field_title.' '.$result);
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
