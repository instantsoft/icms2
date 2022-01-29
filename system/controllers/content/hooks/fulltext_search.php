<?php

class onContentFulltextSearch extends cmsAction {

    public function run($search_controller) {

        $allowed_types = $search_controller->getOption('types');

        $ctypes = $this->model->getContentTypes();

        $sources = array_collection_to_list($ctypes, 'name', 'title');

        foreach ($sources as $name => $title) {
            $sources[$name] = $title;
        }

        // по каким полям поиск
        $match_fields = [];
        // какие поля получать
        $select_fields = [];
        // из каких таблиц выборка
        $table_names= [];
        // какие поля точно нужны
        $default_fields = ['id', 'slug', 'date_pub'];
        // Фильтрация
        $filters = [];
        $_ctypes = [];

        // Поле изображения
        $images_field = [];

        foreach ($ctypes as $ctype) {

            // выключено?
            if ($allowed_types &&
                    !in_array($ctype['name'], $allowed_types)) {
                continue;
            }

            $fields = $this->model->getContentFields($ctype['name']);

            $table_names[$ctype['name']] = $this->model->getContentTypeTableName($ctype['name']);

            $select_fields[$ctype['name']] = $default_fields;

            foreach ($fields as $field) {

                // в настройках полей должно быть включено их участие в индексе
                $is_text = $field['handler']->getOption('in_fulltext_search');

                if ($is_text && !$field['is_private'] && (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))) {

                    $match_fields[$ctype['name']][]  = $field['name'];
                    $select_fields[$ctype['name']][] = $field['name'];
                }

                if (!isset($images_field[$ctype['name']]) && $field['type'] === 'image' &&
                        !$field['is_private'] && $field['is_in_list'] &&
                        (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))) {
                    $select_fields[$ctype['name']][] = $field['name'];

                    $images_field[$ctype['name']] = $field;
                }
            }

            $filters[$ctype['name']] = [
                [
                    'condition' => '=',
                    'value'     => 1,
                    'field'     => 'is_pub'
                ],
                [
                    'condition' => '=',
                    'value'     => 1,
                    'field'     => 'is_approved'
                ],
                [
                    'condition' => 'IS',
                    'value'     => NULL,
                    'field'     => 'is_deleted'
                ],
                [
                    'condition' => 'IS',
                    'value'     => NULL,
                    'field'     => 'is_parent_hidden'
                ]
            ];

            $_ctypes[$ctype['name']] = $ctype;
        }

        return [
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => $filters,
            'item_callback' => function ($item, $model, $sources_name, $match_fields, $select_fields) use ($_ctypes, $images_field) {

                $fields = [];

                foreach ($match_fields as $match_field) {
                    if ($match_field === 'title') {
                        continue;
                    }
                    $fields[$match_field] = $item[$match_field];
                }

                $item['ctype'] = [
                    'name' => $sources_name
                ];

                return array_merge($item, [
                    'url'      => href_to($sources_name, $item['slug'] . '.html'),
                    'ctype'    => $_ctypes[$sources_name],
                    'title'    => $item['title'],
                    'fields'   => $fields,
                    'date_pub' => $item['date_pub'],
                    'image'    => !empty($images_field[$sources_name]) ? $images_field[$sources_name]['handler']->setItem($item)->parseTeaser($item[$images_field[$sources_name]['name']]) : ''
                ]);
            }
        ];
    }

}
