<?php

class onActivityLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_ACTIVITY_CONTROLLER,
            'forms' => [
                'types' => [
                    'title' => LANG_ACTIVITY_TYPES
                ]
            ]
        ];
    }

}
