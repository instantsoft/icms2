<?php

class onCommentsRssFeedList extends cmsAction {

	public function run($feed){

        $category = $author = array();

        $target_controller = $this->request->get('tc', '');
        $target_subject    = $this->request->get('ts', '');
        $target_id         = $this->request->get('ti', '');

        $this->model->filterIsNull('is_deleted')->
                orderBy('date_pub', 'desc')->
                limit($feed['limit']);

        cmsEventsManager::hook('comments_list_filter', $this->model);

        // флаг, что показываем комментарии для записи
        $is_target_list = $target_controller && $target_subject && $target_id;

        if($is_target_list){

            $is_valid = ($this->validate_sysname($target_controller) === true) &&
                        ($this->validate_sysname($target_subject) === true) &&
                        is_numeric($target_id) &&
                        cmsCore::isControllerExists($target_controller) &&
                        cmsCore::isModelExists($target_controller);

            if (!$is_valid){ cmsCore::error404(); }

            $comments = $this->model->filterCommentTarget(
                $target_controller,
                $target_subject,
                $target_id
            );

            $target_model = cmsCore::getModel($target_controller);

            $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);

            if ($target_info){
                $category['title'] = $target_info['title'];
            }

        }

        $comments = $this->model->getComments(function ($item, $model){

            $item['target_title'] = sprintf(LANG_COMMENTS_RSS_TITLE, $item['target_title']);
            $item['page_url'] = href_to_abs($item['target_url']) . '#comment_'.$item['id'];

            return $item;

        });

        $comments = cmsEventsManager::hook('comments_before_list', $comments);

        $feed['items'] = $comments;

        $feed = cmsEventsManager::hook('before_render_'.$feed['ctype_name'].'_feed_list', $feed);

        return array($feed, $category, $author);

    }

}
