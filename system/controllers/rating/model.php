<?php

class modelRating extends cmsModel{

//============================================================================//
//============================================================================//

    public function isUserVoted($vote, $is_logged = true){

        if(!$is_logged){
            $this->filterEqual('ip', $vote['ip']);
        } else {
            $this->filterEqual('user_id', $vote['user_id']);
        }

        $this->filterVotes($vote['target_controller'], $vote['target_subject'], $vote['target_id']);

        $this->useCache('rating.votes');

        $votes_count = $this->getCount('rating_log');

        $this->resetFilters();

        return $votes_count > 0 ? true : false;

    }

    public function filterVotes($controller, $subject, $id){

        $this->filterEqual('target_controller', $controller);
        $this->filterEqual('target_subject', $subject);
        $this->filterEqual('target_id', $id);

        return $this;

    }

//============================================================================//
//============================================================================//

    public function getVotesCount(){
        return $this->getCount('rating_log');
    }

    public function getVotes(){

        $this->useCache('rating.votes');

        $this->joinUser('user_id', array(), 'left');

        return $this->get('rating_log', function($item, $model){

            $item['ip'] = long2ip($item['ip']);

            // формируем номер гостя
            $_okets = explode('.', $item['ip']);

            $item['user'] = array(
                'id'       => $item['user_id'],
                'nickname' => (!empty($item['user_nickname']) ? $item['user_nickname'] : LANG_GUEST.' №'.array_sum($_okets)),
                'avatar'   => $item['user_avatar']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function addVote($vote){

        cmsCache::getInstance()->clean('rating.votes');

        return $this->insert('rating_log', $vote);

    }

//============================================================================//
//============================================================================//

    public function deleteVotes($controller, $subject, $id){

        $this->filterVotes($controller, $subject, $id);

        $this->deleteFiltered('rating_log');

        cmsCache::getInstance()->clean("rating.votes");

    }

    public function deleteUserVotes($user_id){

        cmsCache::getInstance()->clean('rating.votes');

        return $this->delete('rating_log', $user_id, 'user_id');

    }

//============================================================================//
//============================================================================//

}
