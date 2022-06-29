<?php

class modelComments extends cmsModel {

    private $childs = [];

    public function filterCommentTarget($target_controller, $target_subject, $target_id = null) {
        return $this->filterEqual('target_controller', $target_controller)->
            filterEqual('target_subject', $target_subject)->
            filterEqual('target_id', $target_id);
    }

    public function approveComment($id) {

        cmsCache::getInstance()->clean('comments.list');

        return $this->update('comments', $id, [
            'is_approved' => 1
        ]);
    }

    public function updateCommentContent($id, $content, $content_html, $data = []) {

        cmsCache::getInstance()->clean('comments.list');

        return $this->update('comments', $id, array_merge([
            'date_last_modified' => null,
            'content'            => $content,
            'content_html'       => $content_html
        ], $data));
    }

    public function updateCommentsPrivacy($is_private) {

        cmsCache::getInstance()->clean('comments.list');

        return $this->updateFiltered('comments', ['is_private' => $is_private]);
    }

    public function updateCommentsUrl($target_url, $target_title) {

        cmsCache::getInstance()->clean('comments.list');

        return $this->updateFiltered('comments', [
            'target_url'   => $target_url,
            'target_title' => $target_title
        ]);
    }

    /**
     * Удаляет комментарий
     *
     * @param array $comment Данные комментария
     * @param boolean $is_delete Удалять или скрывать
     * @return array Список ID удалённых комментариев
     */
    public function deleteComment($comment, $is_delete = false) {

        $delete_ids = [];

        if (!is_array($comment) && is_numeric($comment)) {
            $comment = $this->getComment($comment);
            if (!$comment) {
                return $delete_ids;
            }
        }

        if ($is_delete) {

            $delete_ids[] = $comment['id'];

            // ищем детей
            $childs = $this->getCommentChildIds($comment['id']);
            if ($childs) {
                foreach ($childs as $child_id) {
                    $delete_ids = array_merge($delete_ids, $this->deleteComment($child_id, true));
                }
            }

            $this->delete('comments', $comment['id']);
            $this->delete('comments_rating', $comment['id'], 'comment_id');

            // Добавляем к списку id детей, id удаляемого комментария
            $comments_ids   = $childs;
            $comments_ids[] = $comment['id'];

            cmsEventsManager::hook('comments_after_delete_list', $comments_ids);

            // обновляем количество
            $comments_count = $this->
                    filterEqual('target_controller', $comment['target_controller'])->
                    filterEqual('target_subject', $comment['target_subject'])->
                    filterEqual('target_id', $comment['target_id'])->
                    getCommentsCount(true);

            cmsCore::getModel($comment['target_controller'])->
                    updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

            // Удаляем изображения
            $paths = string_html_get_images_path($comment['content_html']);
            if ($paths) {

                $files_model = cmsCore::getModel('files');

                foreach ($paths as $path) {

                    $file = $files_model->getFileByPath($path);
                    if (!$file) {
                        continue;
                    }

                    $files_model->deleteFile($file);
                }
            }

            $comment = cmsEventsManager::hook('comments_after_delete', $comment);
        } else {

            $this->update('comments', $comment['id'], ['is_deleted' => 1]);

            $comment = cmsEventsManager::hook('comments_after_hide', $comment);
        }

        cmsCache::getInstance()->clean('comments.list');

        if (!$comment['is_approved']) {
            $comment = cmsEventsManager::hook('comments_after_refuse', $comment);
        }

        return $delete_ids;
    }

    public function restoreUserComments($user_id) {

        cmsCache::getInstance()->clean('comments.list');

        return $this->filterEqual('user_id', $user_id)->updateFiltered('comments', ['is_deleted' => null]);
    }

    public function deleteUserComments($user_id) {

        cmsCache::getInstance()->clean('comments.list');

        $this->filterEqual('user_id', $user_id)->updateFiltered('comments', ['is_deleted' => 1]);

        return $this->delete('comments_tracks', $user_id, 'user_id');
    }

    public function deleteComments($target_controller, $target_subject, $target_id = false) {

        $this->selectOnly('i.id')->select('i.content_html');

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        if ($target_id) {
            $this->filterEqual('target_id', $target_id);
        }

        $this->lockFilters();

        $comments = $this->get('comments', function ($item, $model) {
            return $item['content_html'];
        });

        $this->unlockFilters();

        if (!$comments) {

            $this->resetFilters();

            return false;
        }

        $ids = array_keys($comments);

        $this->deleteFiltered('comments');

        $this->filterIn('comment_id', $ids)->deleteFiltered('comments_rating');

        cmsCache::getInstance()->clean('comments.list');

        // Удаляем изображения
        $files_model = cmsCore::getModel('files');

        foreach ($comments as $content_html) {
            $paths = string_html_get_images_path($content_html);
            if ($paths) {
                foreach ($paths as $path) {

                    $file = $files_model->getFileByPath($path);
                    if (!$file) {
                        continue;
                    }

                   $files_model->deleteFile($file);
                }
            }
        }

        cmsEventsManager::hook('comments_after_delete_list', $ids);

        return true;
    }

    public function setCommentsIsDeleted($target_controller, $target_subject, $target_id, $delete = 1) {

        cmsCache::getInstance()->clean('comments.list');

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        $this->filterEqual('target_id', $target_id);

        return $this->updateFiltered('comments', ['is_deleted' => $delete], true);
    }

    public function addComment($comment) {

        $comment['level']    = 1;
        $comment['ordering'] = 0;

        if ($comment['parent_id'] > 0) {

            $parent_comment = $this->getComment($comment['parent_id']);

            if ($parent_comment) {

                $comment['level'] = $parent_comment['level'] + 1;

                $comment['ordering'] = $this->getNextParentOrdering($parent_comment);
            }

            if (!$comment['ordering']) {
                $comment['ordering'] = $this->getNextThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id']);
            }

            $this->incrementThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id'], $comment['ordering']);
        } else {
            $comment['ordering'] = $this->getNextThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id']);
        }

        cmsCache::getInstance()->clean('comments.list');

        return $this->insert('comments', $comment);

    }

    public function isUserVoted($comment_id, $user_id) {

        $this->selectOnly('i.id');

        $this->filterEqual('comment_id', $comment_id);
        $this->filterEqual('user_id', $user_id);

        return $this->getItem('comments_rating') ? true : false;
    }

    public function rateComment($comment_id, $user_id, $score) {

        $this->insert('comments_rating', [
            'comment_id' => $comment_id,
            'user_id'    => $user_id,
            'score'      => $score
        ]);

        $this->filterEqual('id', $comment_id);

        return $this->increment('comments', 'rating', $score);
    }

    public function getNextParentOrdering($parent_comment) {

        $this->filterCommentTarget($parent_comment['target_controller'], $parent_comment['target_subject'], $parent_comment['target_id']);
        $this->filterLtEqual('level', $parent_comment['level']);
        $this->filterGt('ordering', $parent_comment['ordering']);
        $this->limit(1);

        return $this->getItem('comments', function ($item) {
            return $item['ordering'];
        });
    }

    public function getNextThreadOrdering($target_controller, $target_subject, $target_id) {
        return $this->getMaxThreadOrdering($target_controller, $target_subject, $target_id) + 1;
    }

    public function getMaxThreadOrdering($target_controller, $target_subject, $target_id) {
        return $this->filterCommentTarget($target_controller, $target_subject, $target_id)->getMaxOrdering('comments');
    }

    public function incrementThreadOrdering($target_controller, $target_subject, $target_id, $after) {

        $this->filterCommentTarget($target_controller, $target_subject, $target_id);
        $this->filterGtEqual('ordering', $after);

        return $this->increment('comments', 'ordering');
    }

    public function joinCommentsRating($user_id) {

        if($user_id){

            $this->select('r.score', 'is_rated');

            $this->joinLeft('comments_rating', 'r', "r.comment_id = i.id AND r.user_id='{$user_id}'");
        }

        return $this;
    }

    public function getCommentsCount($reset = false) {

        if (!$this->approved_filter_disabled) {
            $this->filterApprovedOnly();
        }

        $this->useCache('comments.list');

        return $this->getCount('comments', 'id', $reset);
    }

    public function getComments($actions = false) {

        $this->joinUserLeft()->joinSessionsOnline();

        if (!$this->order_by) {
            $this->orderBy('ordering');
        }

        if (!$this->approved_filter_disabled) {
            $this->filterApprovedOnly();
        }

        $this->useCache('comments.list');

        return $this->get('comments', function ($item, $model) use ($actions) {

            $item['is_rated'] = array_key_exists('is_rated', $item) ? $item['is_rated'] : null;

            $item['author_ip'] = string_bintoip($item['author_ip']);

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            ];

            $item['actions'] = [];

            if (is_array($actions)) {
                foreach ($actions as $key => $action) {

                    if (isset($action['handler'])) {
                        $is_active = $action['handler']($item);
                    } else {
                        $is_active = true;
                    }

                    if (!$is_active) { continue; }

                    if (empty($action['href'])) { continue; }

                    if (isset($action['handler_class'])) {
                        $action['class'] = $action['handler_class']($item);
                    }

                    foreach ($item as $cell_id => $cell_value) {

                        if (is_array($cell_value) || is_object($cell_value)) {
                            continue;
                        }
                        if (!$cell_value) { $cell_value = ''; }

                        foreach (['href', 'title', 'hint', 'onclick'] as $replaceable_key) {
                            if(isset($action[$replaceable_key])){
                                $action[$replaceable_key] = str_replace('{' . $cell_id . '}', $cell_value, $action[$replaceable_key]);
                            }
                        }
                    }
                    $item['actions'][$key] = $action;
                }
            }

            return $item;
        });
    }

    public function getComment($id) {

        $this->joinUserLeft()->joinSessionsOnline();

        return $this->getItemById('comments', $id, function ($item, $model) {

            $item['author_ip'] = string_bintoip($item['author_ip']);

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            ];

            return $item;
        });
    }

    public function getCommentChildIds($id, $clear = true) {

        $this->loadCommentChildIds($id);

        if ($this->childs) {

            if ($clear) {

                $return = $this->childs;

                $this->childs = [];

                return $return;
            }

            return $this->childs;
        }

        return $this->childs;
    }

    private function loadCommentChildIds($id) {

        $this->selectOnly('i.id');

        return $this->filterEqual('parent_id', $id)->get('comments', function ($item, $model) {

            $model->childs[] = $item['id'];

            $model->loadCommentChildIds($item['id']);

            return $item['id'];
        });
    }

    public function getTracking($user_id) {

        $this->useCache('comments.tracks');

        $this->filterEqual('user_id', $user_id);

        return $this->getItem('comments_tracks');
    }

    public function addTracking($user_id, $target_controller, $target_subject, $target_id) {

        // Получаем модель целевого контроллера
        $target_model = cmsCore::getModel($target_controller);

        // Получаем URL и заголовок комментируемой страницы
        $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);

        if (!$target_info) {
            return false;
        }

        cmsCache::getInstance()->clean('comments.tracks');

        return $this->insert('comments_tracks', [
            'user_id'           => $user_id,
            'target_controller' => $target_controller,
            'target_subject'    => $target_subject,
            'target_id'         => $target_id,
            'target_url'        => $target_info['url'],
            'target_title'      => $target_info['title']
        ]);
    }

    public function updateTracking($target_controller, $target_subject, $target_id) {

        // Получаем модель целевого контроллера
        $target_model = cmsCore::getModel($target_controller);

        // Получаем URL и заголовок комментируемой страницы
        $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);
        if (!$target_info) {
            return false;
        }

        cmsCache::getInstance()->clean('comments.tracks');

        $this->filterCommentTarget($target_controller, $target_subject, $target_id);

        return $this->updateFiltered('comments_tracks', [
            'target_url'   => $target_info['url'],
            'target_title' => $target_info['title']
        ]);
    }

    public function deleteTracking($id) {

        cmsCache::getInstance()->clean('comments.tracks');

        return $this->delete('comments_tracks', $id);
    }

    public function toggleTracking($is_track, $user_id, $target_controller, $target_subject, $target_id) {

        $track = $this->getTracking($user_id);

        if ($track && $is_track) {
            return true;
        }
        if (!$track && !$is_track) {
            return true;
        }

        if ($track && !$is_track) {
            return $this->deleteTracking($track['id']);
        }

        if (!$track && $is_track) {
            return $this->addTracking($user_id, $target_controller, $target_subject, $target_id);
        }

        return false;
    }

    public function getTrackingUsers() {

        $this->useCache('comments.tracks');

        return $this->get('comments_tracks', function ($item, $model) {
            return $item['user_id'];
        }, false);
    }

    public function getTrackedNewCounts($user_id, $date_after) {

        $this->filterEqual('user_id', $user_id);

        $tracks = $this->get('comments_tracks');

        if (!$tracks) {
            return false;
        }

        $counts    = [];
        $timestamp = strtotime($date_after);

        foreach ($tracks as $track) {

            $this->resetFilters()->
                    filterTimestampGt('date_pub', $timestamp)->
                    filterCommentTarget($track['target_controller'], $track['target_subject'], $track['target_id']);

            $count = $this->getCommentsCount();

            if ($count) {

                $track['count'] = $count;

                $counts[] = $track;
            }
        }

        return $counts;
    }

    public function getGuestLastCommentTime($ip) {

        $time = $this->
                filterIsNull('user_id')->
                filterEqual('author_ip', string_iptobin($ip))->
                orderBy('date_pub', 'desc')->
                getFieldFiltered('comments', 'date_pub');

        return $time ? strtotime($time) : 0;
    }

    public function isRssFeedEnable() {
        return $this->filterEqual('ctype_name', 'comments')->getFieldFiltered('rss_feeds', 'is_enabled');
    }

}
