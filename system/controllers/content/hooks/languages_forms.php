<?php

class onContentLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_CONTENT_CONTROLLER,
            'forms' => [
                'category' => [
                    'title' => LANG_CATEGORIES
                ]
            ]
        ];
    }

}
