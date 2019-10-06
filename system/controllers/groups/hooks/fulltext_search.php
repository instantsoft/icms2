<?php

class onGroupsFulltextSearch extends cmsAction {

    public function run(){

        $sources['groups'] = LANG_GROUPS_CONTROLLER;

        // по каким полям поиск
        $match_fields['groups'] = array();
        // какие поля получать
        $select_fields['groups'] = array('id', 'slug', 'date_pub');
        // из каких таблиц выборка
        $table_names['groups'] = 'groups';

        $fields = $this->loadGroupsFields()->getGroupsFields();

        if($fields){
            foreach($fields as $field){

                // в настройках полей должно быть включено их участие в индексе
                $is_text = $field['handler']->getOption('in_fulltext_search');

                if ($is_text && (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))){

                    $match_fields['groups'][]  = $field['name'];
                    $select_fields['groups'][] = $field['name'];

                }

                if ($field['name'] == 'logo' &&
                        (!$field['groups_read'] || $this->cms_user->isInGroups($field['groups_read']))){
                    $select_fields['groups']['image'] = $field['name'];
                }

            }
        }

        return array(
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => array('groups'=>array()),
            'item_callback' => function($item, $model, $sources_name, $match_fields, $select_fields){

                $item['logo'] = cmsModel::yamlToArray($item['logo']);

                $fields = array();

                foreach ($match_fields as $match_field) {
                    if($match_field == 'title'){ continue; }
                    $fields[$match_field] = $item[$match_field];
                }

                return array_merge($item, array(
                    'url'      => href_to('groups', ($item['slug'] ? $item['slug'] : $item['id'])),
                    'title'    => $item['title'],
                    'fields'   => $fields,
                    'date_pub' => $item['date_pub'],
                    'image'    => (!empty($select_fields['image']) ? html_image($item[$select_fields['image']], 'small', strip_tags($item['title'])) : ''),
                ));

            }
        );

    }

}
