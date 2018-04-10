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

            // категория
            if($target['params']['filters'][0]['field'] == 'category_id'){

                $cat = $this->model->getCategory($ctype['name'], $target['params']['filters'][0]['value']);

                if($cat){

                    $titles[] = $cat['title'];

                    unset($target['params']['filters'][0]);

                }

            }

            foreach ($target['params']['filters'] as $filters) {
                if(isset($fields[$filters['field']])){

                    $result = $fields[$filters['field']]['handler']->getStringValue($filters['value']);

                    if($result){
                        $titles[] = $result;
                    }

                }
            }

        }

        if(!empty($target['params']['field_filters'])){

            foreach ($target['params']['field_filters'] as $field_name => $field_value) {
                if(isset($fields[$field_name])){

                    if($fields[$field_name]['handler']->var_type !== 'array' && is_array($field_value)){
                        foreach ($field_value as $field_val) {

                            $result = $fields[$field_name]['handler']->getStringValue($field_val);

                            if($result){
                                $titles[] = $result;
                            }

                        }
                    } else {

                        $result = $fields[$field_name]['handler']->getStringValue($field_value);

                        if($result){
                            $titles[] = $result;
                        }

                    }

                }
            }

        }

        if(!empty($titles)){
            $result_title .= ' — '.mb_strtolower(implode(', ', array_unique($titles)));
        }

        return $result_title;

    }

}
