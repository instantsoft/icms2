<?php

class modelComments extends cmsModel{

//============================================================================//
//============================================================================//

    public function updateCommentContent($id, $content, $content_html){

        cmsCache::getInstance()->clean("comments.list");

        return $this->update('comments', $id, array(
            'content'=>$content,
            'content_html'=>$content_html
        ));

    }

    public function updateCommentsPrivacy($is_private){

        cmsCache::getInstance()->clean("comments.list");

        return $this->updateFiltered('comments', array('is_private' => $is_private));

    }

//============================================================================//
//============================================================================//

    public function deleteComment($id){

        $this->update('comments', $id, array('is_deleted'=>1));

        cmsCache::getInstance()->clean("comments.list");

    }

    public function deleteUserComments($user_id){

        $this->filterEqual('user_id', $user_id)->updateFiltered('comments', array('is_deleted'=>1));

        $this->delete('comments_tracks', $user_id, 'user_id');

        cmsCache::getInstance()->clean("comments.list");

    }

    public function deleteComments($target_controller, $target_subject, $target_id=false){

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
		
		if ($target_id){
			$this->filterEqual('target_id', $target_id);
		}

        $this->deleteFiltered('comments');

        cmsCache::getInstance()->clean("comments.list");

    }

//============================================================================//
//============================================================================//

    public function addComment($comment){

        if ($comment['parent_id'] == 0){

            $comment['level'] = 1;
            $comment['ordering'] = $this->getNextThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id']);

        } else {

            $parent_comment = $this->getComment($comment['parent_id']);

            $comment['level'] = $parent_comment['level'] + 1;

            $comment['ordering'] = $this->getNextParentOrdering($parent_comment);
            if (!$comment['ordering']) { $comment['ordering'] = $this->getNextThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id']); }

            $this->incrementThreadOrdering($comment['target_controller'], $comment['target_subject'], $comment['target_id'], $comment['ordering']);

        }

        cmsCache::getInstance()->clean("comments.list");

        return $this->insert('comments', $comment);

    }

//============================================================================//
//============================================================================//

    public function isUserVoted($comment_id, $user_id){

        $this->filterEqual('comment_id', $comment_id);
        $this->filterEqual('user_id', $user_id);

        $is_voted = (bool)$this->getCount('comments_rating');

        $this->resetFilters();

        return $is_voted;

    }

    public function rateComment($comment_id, $user_id, $score){

        $this->insert('comments_rating', array(
            'comment_id' => $comment_id,
            'user_id' => $user_id,
            'score' => $score
        ));

        $this->filterEqual('id', $comment_id);

        $this->increment('comments', 'rating', $score);

        return true;

    }

//============================================================================//
//============================================================================//

    public function getNextParentOrdering($parent_comment){

        $this->filterEqual('target_controller', $parent_comment['target_controller']);
        $this->filterEqual('target_subject', $parent_comment['target_subject']);
        $this->filterEqual('target_id', $parent_comment['target_id']);
        $this->filterLtEqual('level', $parent_comment['level']);
        $this->filterGt('ordering', $parent_comment['ordering']);
        $this->limit(1);

        return $this->getItem('comments', function($item){
            return $item['ordering'];
        });

    }

    public function getNextThreadOrdering($target_controller, $target_subject, $target_id){

        return $this->getMaxThreadOrdering($target_controller, $target_subject, $target_id) + 1;

    }

    public function getMaxThreadOrdering($target_controller, $target_subject, $target_id){

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        $this->filterEqual('target_id', $target_id);

        return $this->getMaxOrdering('comments');

    }

    public function incrementThreadOrdering($target_controller, $target_subject, $target_id, $after){

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        $this->filterEqual('target_id', $target_id);
        $this->filterGtEqual('ordering', $after);

        $this->increment('comments', 'ordering');

    }

//============================================================================//
//============================================================================//

    public function getCommentsCount(){

        $count = $this->getCount('comments');

        return $count;

    }

//============================================================================//
//============================================================================//

    public function getComments(){

        $user = cmsUser::getInstance();

        $this->select('r.score', 'is_rated');

        $this->joinUserLeft();
        $this->joinLeft('comments_rating', 'r', "r.comment_id = i.id AND r.user_id='{$user->id}'");

        if (!$this->order_by){
            $this->orderBy('ordering');
        }

        $this->useCache("comments.list");

        return $this->get('comments', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function getComment($id){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->joinUserLeft();

        return $this->getItemById('comments', $id, function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function getTracking($user_id){

        $this->useCache("comments.tracks");

        $this->filterEqual('user_id', $user_id);

        return $this->getItem('comments_tracks');

    }

    public function addTracking($user_id, $target_controller, $target_subject, $target_id){

        // Получаем модель целевого контроллера
        $target_model = cmsCore::getModel( $target_controller );

        // Получаем URL и заголовок комментируемой страницы
        $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);

        if (!$target_info){ return false; }

        cmsCache::getInstance()->clean("comments.tracks");

        return $this->insert('comments_tracks', array(
            'user_id' => $user_id,
            'target_controller' => $target_controller,
            'target_subject' => $target_subject,
            'target_id' => $target_id,
            'target_url' => $target_info['url'],
            'target_title' => $target_info['title'],
        ));

    }

    public function deleteTracking($id){

        cmsCache::getInstance()->clean("comments.tracks");

        return $this->delete('comments_tracks', $id);

    }

    public function toggleTracking($is_track, $user_id, $target_controller, $target_subject, $target_id){

        $track = $this->getTracking($user_id);

        if ($track && $is_track) { return true; }
        if (!$track && !$is_track) { return true; }

        if ($track && !$is_track) { return $this->deleteTracking($track['id']); }

        if (!$track && $is_track) { return $this->addTracking($user_id, $target_controller, $target_subject, $target_id); }

        return false;

    }

    public function getTrackingUsers(){

        $this->useCache("comments.tracks");

        return $this->get('comments_tracks', function($item, $model){
            return $item['user_id'];
        }, false);

    }

    public function getTrackedNewCounts($user_id, $date_after){

        $this->filterEqual('user_id', $user_id);

        $tracks = $this->get('comments_tracks');

        if (!$tracks) { return false; }

        $counts = array();
        $timestamp = strtotime($date_after);


        foreach($tracks as $track){

        $this->
            resetFilters()->
            filterTimestampGt('date_pub', $timestamp)->
            filterEqual('target_controller', $track['target_controller'])->
            filterEqual('target_subject', $track['target_subject'])->
            filterEqual('target_id', $track['target_id']);

            $count = $this->getCommentsCount();

            if ($count){

                $track['count'] = $count;

                $counts[] = $track;

            }

        }

        return $counts;

    }

//============================================================================//
//============================================================================//

    public function getGuestLastCommentTime($ip){
        
        $time = $this->
                    filterEqual('user_id', 0)->
                    filterEqual('author_url', $ip)->
                    orderBy('date_pub', 'desc')->
                    getFieldFiltered('comments', 'date_pub');
        
        return strtotime($time);
        
    }
    
}
