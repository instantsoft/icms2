<?php

class onContentRssFeedList extends cmsAction {

	public function run($feed){

        $category_id = $this->request->get('category', false);
        $user_id     = $this->request->get('user', false);

        $category = $author = array();

        if ($category_id){
            $category = $this->model->getCategory($feed['ctype_name'], $category_id);
        }

        if ($user_id){
            $author = cmsCore::getModel('users')->getUser($user_id);
        }

        if (!empty($category)){
            $this->model->filterCategory($feed['ctype_name'], $category, true);
        }

        if (!empty($author)){
            $this->model->filterEqual('user_id', $user_id);
        }

        $this->model->limit($feed['limit']);

        $feed['items'] = $this->model->getContentItems($feed['ctype_name']);

        $feed = cmsEventsManager::hook('before_render_'.$feed['ctype_name'].'_feed_list', $feed);

        return array($feed, $category, $author);

    }

}
