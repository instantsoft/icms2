<?php

class onUsersUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if ($tab_name == 'friends'){

            if(empty($this->options['is_friends_on'])){
                return false;
            }

            // Проверяем наличие друзей
            $this->friends_count = $profile['friends_count'];
            if (!$this->friends_count) { return false; }

            return array('counter' => $this->friends_count);

        }

        if ($tab_name == 'subscribers'){

            $this->subscribers_count = $profile['subscribers_count'];
            if (!$this->subscribers_count) { return false; }

            return array('counter' => $this->subscribers_count);

        }

        return true;

    }

}
