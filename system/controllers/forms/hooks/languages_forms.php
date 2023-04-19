<?php

class onFormsLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_FORMS_CONTROLLER,
            'forms' => [
                'form' => [
                    'title' => LANG_FORMS_CP_FORMS
                ],
                'field' => [
                    'title' => LANG_CP_CTYPE_FIELDS
                ]
            ]
        ];
    }

}
