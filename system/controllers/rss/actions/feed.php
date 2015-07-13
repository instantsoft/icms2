<?php

class actionRssFeed extends cmsAction {

    public function run($ctype_name=false){

        if (!$ctype_name) { cmsCore::error404(); }

        $feed = $this->model->getFeedByCtypeName($ctype_name);
        if (!$feed || !$feed['is_enabled']) { cmsCore::error404(); }

        $category_id = $this->request->get('category', false);
        $user_id = $this->request->get('user', false);

        $content_model = cmsCore::getModel('content');

        $content_model->
                orderBy('id', 'desc')->
                limit($feed['limit']);

        if ($category_id){

            $category = $content_model->getCategory($ctype_name, $category_id);

            if ($category){
                $content_model->filterCategory($ctype_name, $category, true);
            }

        }

        if ($user_id){

            $users_model = cmsCore::getModel('users');
            $author = $users_model->getUser($user_id);

            if ($author){
                $content_model->filterEqual('user_id', $user_id);
            }

        }

        $feed['items'] = $content_model->getContentItems($ctype_name);

		header('Content-type: application/rss+xml; charset=utf-8');
		
        return cmsTemplate::getInstance()->renderPlain('feed', array(
            'feed' => $feed,
            'category' => isset($category) ? $category : false,
            'author' => isset($author) ? $author : false
        ));

    }

}
