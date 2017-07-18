<?php

class onUsersUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if ($tab_name == 'friends'){

            if(empty($this->options['is_friends_on'])){
                return false;
            }

            // Проверяем наличие друзей
            $this->friends_count = $this->model->getFriendsCount($profile['id']);
            if (!$this->friends_count) { return false; }

            return array('counter' => $this->friends_count);

        }

        return true;

    }

}
