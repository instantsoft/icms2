<?php

class onAuthFrontpageTypes extends cmsAction {

    public function run(){

        return array(
            'name'  => $this->name,
            'types' => array(
                'auth:login' => LANG_CP_SETTINGS_FP_SHOW_PROFILE
            )
        );

    }

}
