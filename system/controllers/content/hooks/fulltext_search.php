<?php

class onContentFulltextSearch extends cmsAction {

    public function run(){

        $ctypes = $this->model->getContentTypes();

        $sources = array_collection_to_list($ctypes, 'name', 'title');

        foreach($sources as $name=>$title){
            $sources[$name] = $title;
        }

        // по каким полям поиск
        $match_fields = array();
        // какие поля получать
        $select_fields = array();
        // из каких таблиц выборка
        $table_names = array();
        // какие поля точно нужны
        $default_fields = array('id', 'slug', 'date_pub');

        foreach($ctypes as $ctype){

            $fields = $this->model->getContentFields($ctype['name']);

            $table_names[$ctype['name']] = $this->model->getContentTypeTableName($ctype['name']);

            $select_fields[$ctype['name']] = $default_fields;

            foreach($fields as $field){

                // в настройках полей должно быть включено их участие в индексе
                $is_text = $field['handler']->getOption('in_fulltext_search');

                if ($is_text && !$field['is_private'] && (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))){

                    $match_fields[$ctype['name']][]  = $field['name'];
                    $select_fields[$ctype['name']][] = $field['name'];

                }

                if ($field['type'] == 'image' &&
                        !$field['is_private'] &&
                        (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))){
                    $select_fields[$ctype['name']]['image'] = $field['name'];
                }

            }

            $filters[$ctype['name']] = array(
                array(
                    'condition' => '=',
                    'value'     => 1,
                    'field'     => 'is_pub'
                ),
                array(
                    'condition' => '=',
                    'value'     => 1,
                    'field'     => 'is_approved'
                ),
                array(
                    'condition' => 'IS',
                    'value'     => NULL,
                    'field'     => 'is_deleted'
                ),
                array(
                    'condition' => 'IS',
                    'value'     => NULL,
                    'field'     => 'is_parent_hidden'
                )
            );

            $_ctypes[$ctype['name']] = $ctype;

        }

        return array(
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => $filters,
            'item_callback' => function($item, $model, $sources_name, $match_fields, $select_fields) use ($_ctypes){

                $fields = array();

                foreach ($match_fields as $match_field) {
                    if($match_field == 'title'){ continue; }
                    $fields[$match_field] = $item[$match_field];
                }

                return array_merge($item, array(
                    'url'      => href_to($sources_name, $item['slug'] . '.html'),
                    'ctype'    => $_ctypes[$sources_name],
                    'title'    => $item['title'],
                    'fields'   => $fields,
                    'date_pub' => $item['date_pub'],
                    'image'    => (!empty($select_fields['image']) ? html_image($item[$select_fields['image']], 'small', strip_tags($item['title'])) : ''),
                ));

            }
        );

    }

}
