<?php

class modelModeration extends cmsModel{

    public function getTasksCounts($moderator_id, $is_admin = false){

        if(!$is_admin){
            $this->filterEqual('moderator_id', $moderator_id);
        }

        $tasks = $this->getTasks();
        if (!$tasks) { return false; }

        $counts = array();

        foreach($tasks as $task){

            if (!isset($counts[$task['ctype_name']])){
                $counts[$task['ctype_name']] = 1;
            } else {
                $counts[$task['ctype_name']]++;
            }

        }

        return $counts;

    }

    public function getTasksCount(){

        return $this->getCount('moderators_tasks');

    }

    public function getTasks(){

        return $this->get('moderators_tasks', function ($item, $model){
            $item['url'] = rel_to_href($item['url']);
            return $item;
        });

    }

    public function isUserModerator($user_id){

        $this->filterEqual('user_id', $user_id);

        $is_moderator = (bool)$this->getCount('moderators');

        $this->resetFilters();

        return $is_moderator;

    }

}
