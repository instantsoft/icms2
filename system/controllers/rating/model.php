<?php

class modelRating extends cmsModel{

//============================================================================//
//============================================================================//

    public function isUserVoted($vote){

        $this->filterEqual('user_id', $vote['user_id']);
        $this->filterEqual('target_controller', $vote['target_controller']);
        $this->filterEqual('target_subject', $vote['target_subject']);
        $this->filterEqual('target_id', $vote['target_id']);

        $this->useCache("rating.votes");

        $votes_count = $this->getCount('rating_log');

        $this->resetFilters();

        return $votes_count > 0 ? true : false;

    }

//============================================================================//
//============================================================================//

    public function filterVotes($controller, $subject, $id){

        $this->filterEqual('target_controller', $controller);
        $this->filterEqual('target_subject', $subject);
        $this->filterEqual('target_id', $id);

        return $this;

    }

    public function getVotesCount(){

        return $this->getCount('rating_log');

    }

    public function getVotes(){

        $this->useCache("rating.votes");

        $this->joinUser();

        return $this->get('rating_log', function($item, $model){

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

    public function addVote($vote){

        cmsCache::getInstance()->clean("rating.votes");

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

        cmsCache::getInstance()->clean("rating.votes");

        return $this->delete('rating_log', $user_id, 'user_id');

    }

//============================================================================//
//============================================================================//

}
