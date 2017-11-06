<?php

class onModerationContentAfterTrashPut extends cmsAction {

    public function run($data){

        list($ctype_name, $item) = $data;

        $date_expired = false;

        if($this->cms_user->is_logged){

            $trash_left_time = intval(cmsUser::getPermissionValue($ctype_name, 'trash_left_time'));

            $moderator = $this->model->filterEqual('ctype_name', $ctype_name)->
                    filterEqual('user_id', $this->cms_user->id)->
                    getItem('moderators');
            if($moderator){
                if($moderator['trash_left_time'] !== null){
                    $trash_left_time = intval($moderator['trash_left_time']);
                }
            }

            if($trash_left_time){
                $date_expired = date('Y-m-d H:i:s', (time() + ($trash_left_time*60*60)));
            }

        }

        $this->model->log(modelModeration::LOG_TRASH_ACTION, array(
            'moderator_id'      => ($this->cms_user->is_logged ? $this->cms_user->id : null),
            'author_id'         => $item['user_id'],
            'date_expired'      => $date_expired,
            'target_id'         => $item['id'],
            'target_controller' => 'content',
            'target_subject'    => $ctype_name,
            'data'              => array(
                'title'   => $item['title'],
                'slug'    => $item['slug']
            )
        ));

        return $data;

    }

}
