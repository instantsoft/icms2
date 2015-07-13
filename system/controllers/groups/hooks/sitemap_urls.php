<?php

class onGroupsSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $groups = $this->model->
                            limit(false)->
                            getGroupsIds();

        if ($groups){
            foreach($groups as $group){
                $url = href_to_abs($this->name, $group['id']);
                $date_last_modified = false;
                $urls[$url] = $date_last_modified;
            }
        }

        return $urls;

    }

}
