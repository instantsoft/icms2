<?php

class onContentTagsSearchSubjects extends cmsAction {

    public function run($data){

        list($tag, $targets) = $data;

        $menu_items = array();

        if(empty($targets[$this->name])){
            return $menu_items;
        }

        $ctype_names = array_unique($targets[$this->name]);

        // Согласно сортировки типов контента
        $ctypes = $this->model->getContentTypes();

        foreach($ctypes as $ctype){

            if(!in_array($ctype['name'], $ctype_names)){
                continue;
            }

            $key = $this->name.'-'.$ctype['name'];

            $menu_items[$key] = array(
                'title' => $ctype['title'],
                'url'   => href_to('tags', $key, array(urlencode($tag['tag'])))
            );
        }

        return $menu_items;
    }

}
