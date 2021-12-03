<?php

class onUsersUserTabInfo extends cmsAction {

    public function run($profile, $tab_name) {

        if ($tab_name == 'karma') {
            if (!$this->options['is_karma']) {
                return false;
            }
        }

        if ($tab_name == 'friends') {

            if (empty($this->options['is_friends_on'])) {
                return false;
            }

            // Проверяем наличие друзей
            $this->friends_count = $profile['friends_count'];
            if (!$this->friends_count) {
                return false;
            }

            return ['counter' => $this->friends_count];
        }

        if ($tab_name == 'subscribers') {

            $this->subscribers_count = $profile['subscribers_count'];
            if (!$this->subscribers_count) {
                return false;
            }

            return ['counter' => $this->subscribers_count];
        }

        // Не используется. Совместимость с InstantCMS 1.X.
        // Для работы необходим экшен и запись в таблице.
        if ($tab_name == 'files') {

            $this->model->filterEqual('target_controller', 'users');
            $this->model->filterEqual('target_subject', 'files');
            $this->model->filterEqual('user_id', $profile['id']);

            $this->files_count = $this->model->getCount('uploaded_files', 'id', true);

            if (!$this->files_count) {
                return false;
            }

            return ['counter' => $this->files_count];
        }

        return true;
    }

}
