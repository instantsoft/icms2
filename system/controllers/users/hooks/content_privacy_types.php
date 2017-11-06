<?php

class onUsersContentPrivacyTypes extends cmsAction {

    public function run($data){

        if (!empty($this->options['is_friends_on'])){

            return array(
                'name'  => $this->name,
                'types' => array(
                    1 => LANG_PRIVACY_PRIVATE
                )
            );

        }

        return false;

    }

}
