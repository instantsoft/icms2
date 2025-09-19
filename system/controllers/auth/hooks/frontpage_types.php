<?php

class onAuthFrontpageTypes extends cmsAction {

    public function run() {

        return [
            'name'  => $this->name,
            'types' => [
                'auth:login' => LANG_CP_SETTINGS_FP_SHOW_PROFILE
            ]
        ];
    }

}
