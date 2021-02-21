<?php

class onContentTagsSearch extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($ctype_name, $tag, $page_url) {

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return '';
        }

        $this->model->
                join('tags_bind', 't', "t.target_id = i.id AND t.target_subject = '{$ctype['name']}' AND t.target_controller = 'content'")->
                filterEqual('t.tag_id', $tag['id']);

        return $this->setListContext('search')->renderItemsList($ctype, $page_url);
    }

}
