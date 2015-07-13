<?php

class onGroupsRatingVote extends cmsAction {

    public function run($data){

        // Обновляем суммарный рейтинг группы
        if (!empty($data['target']['parent_type'])){
            if ($data['target']['parent_type']=='group'){
                $groups_model = cmsCore::getModel('groups');
                $groups_model->updateGroupRating($data['target']['parent_id'], $data['vote']['score']);
            }
        }

        return $data;

    }

}
