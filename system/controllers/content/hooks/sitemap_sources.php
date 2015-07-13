<?php

class onContentSitemapSources extends cmsAction {

    public function run(){

        $ctypes = $this->model->getContentTypes();

        $sources = array_collection_to_list($ctypes, 'name', 'title');

        foreach($sources as $name=>$title){ $sources[$name] = LANG_CONTENT_CONTROLLER . ': ' . $title; }

        return array(
            'name' => $this->name,
            'sources' => $sources
        );

    }

}
