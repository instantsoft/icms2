<?php

class modelModeration extends cmsModel {

    const LOG_TRASH_ACTION = 0;
    const LOG_DELETE_ACTION = 1;
    const LOG_RESTORE_ACTION = 2;

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

    public function getTargetModeratorData($id){

        return $this->getItemById('moderators', $id);

    }

    public function log($action, $data){

        $data['action'] = $action;

        return $this->insert('moderators_logs', $data);

    }

    public function logUpdateTarget($target_controller, $target_subject, $target_id, $data){

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        $this->filterEqual('target_id', $target_id);

        return $this->updateFiltered('moderators_logs', $data, true);


    }

    public function logDeleteTarget($target_controller, $target_subject, $target_id){

        $this->filterEqual('target_controller', $target_controller);
        $this->filterEqual('target_subject', $target_subject);
        $this->filterEqual('target_id', $target_id);

        return $this->deleteFiltered('moderators_logs');


    }

	public function deleteExpiredTrashContentItems(){

        return $this->filterNotNull('date_expired')->
                    filterEqual('i.action', self::LOG_TRASH_ACTION)->
                    filter('i.date_expired <= NOW()')->
                    get('moderators_logs', function($item, $model) {
                        cmsCore::getModel($item['target_controller'])->deleteContentItem($item['target_subject'], $item['target_id']);
                        $model->delete('moderators_logs', $item['id']);
                        return $item['id'];
                    });

	}

}
