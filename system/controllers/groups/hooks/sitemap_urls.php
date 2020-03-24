<?php

class onGroupsSitemapUrls extends cmsAction {

    public function run($type){

        $urls = array();

        if ($type != 'profiles') { return $urls; }

        $this->model->selectOnly('i.id', 'id')->select('i.slug', 'slug')->select('i.title', 'title');
		$this->model->filterNotEqual('i.is_closed', 1);

        $groups = $this->model->limit(false)->getGroups();

        if ($groups){
            foreach($groups as $group){
                $urls[] = array(
                    'last_modified' => null,
                    'title'         => $group['title'],
                    'url'           => href_to_abs($this->name, $group['slug'])
                );
            }
        }

        return $urls;

    }

}
