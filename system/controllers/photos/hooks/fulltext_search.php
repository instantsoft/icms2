<?php

class onPhotosFulltextSearch extends cmsAction {

    public function run(){

        $sources['photos'] = LANG_PHOTOS;

        // по каким полям поиск
        $match_fields['photos'] = array('title', 'content');
        // какие поля получать
        $select_fields['photos'] = array('id', 'content', 'image', 'slug', 'date_pub', 'title', 'rating', 'comments', 'user_id', 'sizes', 'hits_count');
        // из каких таблиц выборка
        $table_names['photos'] = 'photos';

        // получаем тут высоту строк, чтобы в шаблоне потом забрать
        $this->getRowHeight();

        return array(
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'filters'       => array('photos'=>array()),
            'item_callback' => function($item, $model, $sources_name, $match_fields, $select_fields){

                $item['image'] = cmsModel::yamlToArray($item['image']);
                $item['sizes'] = cmsModel::yamlToArray($item['sizes']);
                $item['title'] = strip_tags($item['title']);

                return $item;

            }
        );

    }

}
