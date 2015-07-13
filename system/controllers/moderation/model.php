<?php

class modelModeration extends cmsModel{

    public function getTasksCounts($moderator_id){

       $tasks = $this->filterEqual('moderator_id', $moderator_id)->getTasks();

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

    public function getTasks(){

        return $this->get('moderators_tasks');

    }

    public function isUserModerator($user_id){

        $this->filterEqual('user_id', $user_id);

        $is_moderator = (bool)$this->getCount('moderators');

        $this->resetFilters();

        return $is_moderator;

    }

}
