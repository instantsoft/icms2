<?php

class onContentTagsSearchSubjects extends cmsAction {

    public function run($data){

        list($tag, $targets, $target) = $data;

        $menu_items = array();

        if(empty($targets[$this->name])){
            return $menu_items;
        }

        $ctype_names = array_unique($targets[$this->name]);

        foreach($ctype_names as $ctype_name){

            $ctype = $this->model->getContentTypeByName($ctype_name);
            if(!$ctype){ continue; }

            $key = $this->name.'-'.$ctype['name'];

            $menu_items[$key] = array(
                'title' => $ctype['title'],
                'url'   => href_to('tags', $key, array(urlencode($tag['tag'])))
            );

        }

        return $menu_items;

    }

}
