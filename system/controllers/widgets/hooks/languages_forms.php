<?php

class onWidgetsLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_CP_SECTION_WIDGETS,
            'forms' => [
                'options_full_form' => [
                    'title' => LANG_CP_SECTION_SETTINGS
                ]
            ]
        ];
    }

}
