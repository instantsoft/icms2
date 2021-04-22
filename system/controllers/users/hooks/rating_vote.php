<?php

class onUsersRatingVote extends cmsAction {

    public function run($data) {

        // Обновляем суммарный рейтинг пользователя
        if (!empty($data['target']['user_id'])) {
            $this->model->updateUserRating($data['target']['user_id'], $data['vote']['score']);
        }

        return $data;
    }

}
