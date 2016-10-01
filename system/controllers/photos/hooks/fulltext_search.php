<?php

class onPhotosFulltextSearch extends cmsAction {

    public function run(){

        $sources['photos'] = LANG_PHOTOS;

        // по каким полям поиск
        $match_fields['photos'] = array('title');
        // какие поля получать
        $select_fields['photos'] = array('id', 'image', 'date_pub', 'title', 'rating', 'comments');
        // из каких таблиц выборка
        $table_names['photos'] = 'photos';

        return array(
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => array('photos'=>array()),
            'item_callback' => function($item, $model, $sources_name, $match_fields, $select_fields){

                return array_merge($item, array(
                    'url'      => href_to($sources_name, 'view', $item['id']),
                    'title'    => $item['title'],
                    'fields'   => array(),
                    'date_pub' => $item['date_pub'],
                    'image'    => html_image($item['image'], 'normal', strip_tags($item['title']))
                ));

            }
        );

    }

}
