<?php

class modelModeration extends cmsModel {

    const LOG_TRASH_ACTION = 0;
    const LOG_DELETE_ACTION = 1;
    const LOG_RESTORE_ACTION = 2;

    public function getUserTasksCounts($user_id){

        $this->filterEqual('author_id', $user_id);

        return $this->getTasksItemCounts();

    }

    public function getTasksCounts($moderator_id, $is_admin = false){

        if(!$is_admin){
            $this->filterEqual('moderator_id', $moderator_id);
        }

        return $this->getTasksItemCounts();

    }

    public function getTasksItemCounts(){

        $this->selectOnly('ctype_name');

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
            if(isset($item['url'])){
                $item['url'] = rel_to_href($item['url']);
            }
            return $item;
        }, false);

    }

    public function getModeratorTask($controller_name, $id){

        return $this->filterEqual('ctype_name', $controller_name)->
                    filterEqual('item_id', $id)->
                    getItem('moderators_tasks');

    }

    public function cancelModeratorTask($controller_name, $id, $moderator_user_id){

        $task = $this->getModeratorTask($controller_name, $id);
        if(!$task){ return false; }

        $this->
            filterEqual('user_id', $task['moderator_id'])->
            filterEqual('ctype_name', $controller_name)->
            filterGt('count_idle', 0)->
            decrement('moderators', 'count_idle');

        if($moderator_user_id != $task['moderator_id']){
            $this->
                filterEqual('user_id', $moderator_user_id)->
                filterEqual('ctype_name', $controller_name)->
                filterGt('count_idle', 0)->
                decrement('moderators', 'count_idle');
        }

        $this->
            filterEqual('ctype_name', $controller_name)->
            filterEqual('item_id', $id)->
            deleteFiltered('moderators_tasks');

        return $task;

    }

    public function closeModeratorTask($controller_name, $id, $is_approved, $moderator_user_id){

        $counter_field = $is_approved ? 'count_approved' : 'count_deleted';

        $task = $this->getModeratorTask($controller_name, $id);
        if(!$task){ return false;}

        $this->
            filterEqual('user_id', $moderator_user_id)->
            filterEqual('ctype_name', $controller_name)->
            increment('moderators', $counter_field);

        $this->
            filterEqual('user_id', $task['moderator_id'])->
            filterEqual('ctype_name', $controller_name)->
            filterGt('count_idle', 0)->
            decrement('moderators', 'count_idle');

        if($moderator_user_id != $task['moderator_id']){
            $this->
                filterEqual('user_id', $moderator_user_id)->
                filterEqual('ctype_name', $controller_name)->
                filterGt('count_idle', 0)->
                decrement('moderators', 'count_idle');
        }

        return $this->
                filterEqual('ctype_name', $controller_name)->
                filterEqual('item_id', $id)->
                deleteFiltered('moderators_tasks');

    }

    public function userIsContentModerator($ctype_name, $user_id){

        if(!$user_id){ return false; }

        $this->filterEqual('ctype_name', $ctype_name);
        $this->filterEqual('user_id', $user_id);

        return (bool)$this->getFieldFiltered('moderators', 'id');

    }

    public function isUserModerator($user_id){

        $this->filterEqual('user_id', $user_id);

        return (bool)$this->getFieldFiltered('moderators', 'id');

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

    public function getNextModeratorId($controller_name){

        $id = $this->filterEqual('ctype_name', $controller_name)->
                    orderBy('count_idle', 'asc')->
                    getFieldFiltered('moderators', 'user_id');

        if (!$id){

            $id = $this->filterEqual('is_admin', 1)->
                        getFieldFiltered('{users}', 'id');
            // проверяем наличие администратора в таблице модераторов
            // и если его там нет, добавляем
            if(!$this->filterEqual('ctype_name', $controller_name)->getItemByField('moderators', 'user_id', $id)){
                $this->addContentTypeModerator($controller_name, $id);
            }

        }

        return $id;

    }

    public function isModeratorTaskExists($controller_name, $user_id){

        $this->filterDateYounger('date_pub', 1);

        return $this->filterEqual('ctype_name', $controller_name)->
                    filterEqual('moderator_id', $user_id)->
                    getFieldFiltered('moderators_tasks', 'id');
    }

    public function addModeratorTask($controller_name, $user_id, $is_new_item, $item){

        $this->
            filterEqual('user_id', $user_id)->
            filterEqual('ctype_name', $controller_name)->
            increment('moderators', 'count_idle');

        return $this->insert('moderators_tasks', array(
            'moderator_id' => $user_id,
            'author_id'    => $item['user_id'],
            'item_id'      => $item['id'],
            'ctype_name'   => $controller_name,
            'title'        => $item['title'],
            'url'          => (isset($item['url']) ? $item['url'] : href_to_rel($controller_name, $item['slug'] . '.html')),
            'date_pub'     => '',
            'is_new_item'  => $is_new_item
        ));

    }

    public function getContentTypeModerators($ctype_name){

        $this->joinUser();

        $this->filterEqual('ctype_name', $ctype_name);

        $this->orderBy('id');

        return $this->get('moderators', function($item, $model){
            $item['user'] = array(
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => $item['user_avatar'],
                'groups'    => $item['user_groups']
            );
            return $item;
        }, 'user_id');
    }

    public function getContentTypeModerator($id){

        $this->joinUser();

        return $this->getItemById('moderators', $id, function($item, $model){
            $item['user'] = array(
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => $item['user_avatar'],
                'groups'    => $item['user_groups']
            );
            return $item;
        });
    }

    public function addContentTypeModerator($ctype_name, $user_id){

        $id = $this->insert('moderators', array(
            'ctype_name'    => $ctype_name,
            'user_id'       => $user_id,
            'date_assigned' => ''
        ));

        return $this->getContentTypeModerator($id);

    }

    public function deleteContentTypeModerator($ctype_name, $user_id){

        return $this->
                    filterEqual('ctype_name', $ctype_name)->
                    filterEqual('user_id', $user_id)->
                    deleteFiltered('moderators');

    }

}
