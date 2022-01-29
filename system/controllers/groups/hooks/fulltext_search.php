<?php

class onGroupsFulltextSearch extends cmsAction {

    public function run() {

        $sources['groups'] = LANG_GROUPS_CONTROLLER;

        // по каким полям поиск
        $match_fields['groups']  = [];
        // какие поля получать
        $select_fields['groups'] = ['id', 'slug', 'date_pub'];
        // из каких таблиц выборка
        $table_names['groups']   = 'groups';
        // Поле изображения
        $image_field = null;

        $fields = $this->loadGroupsFields()->getGroupsFields();

        if ($fields) {
            foreach ($fields as $field) {

                // в настройках полей должно быть включено их участие в индексе
                $is_text = $field['handler']->getOption('in_fulltext_search');

                if ($is_text && (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))) {

                    $match_fields['groups'][]  = $field['name'];
                    $select_fields['groups'][] = $field['name'];
                }

                if ($image_field === null && $field['type'] === 'image' &&
                        !$field['is_private'] && $field['is_in_list'] &&
                        (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))) {

                    $select_fields['groups'][] = $field['name'];

                    $image_field = $field;
                }
            }
        }

        return [
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => ['groups' => []],
            'item_callback' => function ($item, $model, $sources_name, $match_fields, $select_fields) use($image_field) {

                $fields = [];

                foreach ($match_fields as $match_field) {
                    if ($match_field === 'title') {
                        continue;
                    }
                    $fields[$match_field] = $item[$match_field];
                }

                $item['ctype'] = [
                    'name' => 'groups'
                ];

                return array_merge($item, [
                    'url'      => href_to('groups', ($item['slug'] ? $item['slug'] : $item['id'])),
                    'title'    => $item['title'],
                    'fields'   => $fields,
                    'date_pub' => $item['date_pub'],
                    'image'    => $image_field ? $image_field['handler']->setItem($item)->parseTeaser($item[$image_field['name']]) : ''
                ]);
            }
        ];
    }

}
