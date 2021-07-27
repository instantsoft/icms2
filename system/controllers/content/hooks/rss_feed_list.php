<?php

class onContentRssFeedList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($feed) {

        $category_id = $this->request->get('category', 0);
        $user_id     = $this->request->get('user', 0);

        $category = $author = [];

        if ($category_id) {
            $category = $this->model->getCategory($feed['ctype_name'], $category_id);
        }

        if ($user_id) {
            $author = cmsCore::getModel('users')->getUser($user_id);
        }

        if (!empty($category)) {
            $this->model->filterCategory($feed['ctype_name'], $category, true, true);
        }

        if (!empty($author)) {
            $this->model->filterEqual('user_id', $user_id);
        }

        $this->model->limit($feed['limit']);

        list ($feed, $category, $author, $this->model) = cmsEventsManager::hook('content_list_rss_filter', [$feed, $category, $author, $this->model]);
        list ($feed, $category, $author, $this->model) = cmsEventsManager::hook("content_{$feed['ctype_name']}_list_rss_filter", [$feed, $category, $author, $this->model]);

        $feed['items'] = $this->model->getContentItems($feed['ctype_name'], function ($item, $model, $ctype_name) {

            $item['page_url']     = href_to_abs($ctype_name, $item['slug'] . '.html');
            $item['comments_url'] = $item['page_url'] . '#comments';

            return $item;
        });

        $feed = cmsEventsManager::hook('before_render_' . $feed['ctype_name'] . '_feed_list', $feed);

        return [$feed, $category, $author];
    }

}
