<?php

class onUsersSitemapSources extends cmsAction {

    public function run(){

        return array(
            'name' => $this->name,
            'sources' => array('profiles' => LANG_USERS_CONTROLLER)
        );

    }

}
