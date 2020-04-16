<?php

class onActivityUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if($profile['id'] == $this->cms_user->id){
            return array(
                'title'=>LANG_ACTIVITY_TAB_MY
            );
        }

        return true;

    }

}
