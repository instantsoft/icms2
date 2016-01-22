<?php

class onActivityUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if($profile['id'] == cmsUser::getInstance()->id){
            return array(
                'title'=>LANG_ACTIVITY_TAB_MY
            );
        }

        return true;

    }

}
