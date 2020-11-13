<?php

class onContentDbNestedTables extends cmsAction {

    public function run(){

        $types = [];

        $ctypes = $this->model->getContentTypesFiltered();

        if ($ctypes) {
            foreach ($ctypes as $ctype) {
                $types[$this->model->getContentCategoryTableName($ctype['name'])] = $ctype['title'];
            }
        }

        return $types;
    }

}
