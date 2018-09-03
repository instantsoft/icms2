<?php

class onContentTagsSearch extends cmsAction {

    public function run($ctype_name, $page_url){

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if(!$ctype){
            return '';
        }

        return $this->setListContext('search')->renderItemsList($ctype, $page_url);

    }

}
