<?php

class onGroupsSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $this->model->selectOnly('i.id', 'id')->select('i.slug', 'slug');
		$this->model->filterNotEqual('i.is_closed', 1);

        $groups = $this->model->limit(false)->getGroups();

        if ($groups){
            foreach($groups as $group){
                $url = href_to_abs($this->name, $group['slug']);
                $date_last_modified = false;
                $urls[$url] = $date_last_modified;
            }
        }

        return $urls;

    }

}
