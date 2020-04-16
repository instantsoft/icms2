<?php

class onGroupsSitemapSources extends cmsAction {

    public function run(){

        return array(
            'name' => $this->name,
            'sources' => array('profiles' => LANG_GROUPS_CONTROLLER)
        );

    }

}
