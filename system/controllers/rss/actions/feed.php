<?php

class actionRssFeed extends cmsAction {

    public function run($ctype_name=false){

        if (!$ctype_name) { cmsCore::error404(); }

        $feed = $this->model->getFeedByCtypeName($ctype_name);
        if (!$feed || !$feed['is_enabled']) { cmsCore::error404(); }

        $category_id = $this->request->get('category', false);
        $user_id     = $this->request->get('user', false);

        $content_model = cmsCore::getModel('content');

        if ($category_id){
            $category = $content_model->getCategory($ctype_name, $category_id);
        }

        if ($user_id){
            $author = cmsCore::getModel('users')->getUser($user_id);
        }

        if (!empty($category)){
            $content_model->filterCategory($ctype_name, $category, true);
        }

        if (!empty($author)){
            $content_model->filterEqual('user_id', $user_id);
        }

        $content_model->orderBy('id', 'desc')->limit($feed['limit']);

        $feed['items'] = $content_model->getContentItems($ctype_name);

        $feed = cmsEventsManager::hook('before_render_'.$ctype_name.'_feed_list', $feed);

		header('Content-type: application/rss+xml; charset=utf-8');

        return cmsTemplate::getInstance()->renderPlain('feed', array(
            'feed'     => $feed,
            'category' => isset($category) ? $category : false,
            'author'   => isset($author) ? $author : false
        ));

    }

}
