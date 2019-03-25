<?php

class onWallUserDelete extends cmsAction {

    public function run($user){

        // получаем записи-ответы
        $this->model->selectOnly('parent_id')->limit(false)->
                filterEqual('user_id', $user['id'])->
                filterNotEqual('parent_id', 0);

        $entries_ids = $this->model->get('wall_entries', function($item, $model){ return $item['parent_id']; }, 'parent_id');

        if($entries_ids){

            $entries_status_ids = $this->model->selectOnly('status_id')->limit(false)->
                    filterNotNull('status_id')->filterIn('id', $entries_ids)->
                    get('wall_entries', function($item, $model){ return $item['status_id']; }, 'status_id');

            if($entries_status_ids){
                foreach ($entries_status_ids as $status_id) {

                    $this->model->filterEqual('id', $status_id);
                    $this->model->decrement('{users}_statuses', 'replies_count');

                }
            }

        }

        $this->model->deleteUserEntries($user['id']);

        return $user;

    }

}
