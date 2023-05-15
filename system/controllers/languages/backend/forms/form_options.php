<?php

class formLanguagesOptions extends cmsForm {

    public function init() {

        $childs = [];

        $languages_form_fields = cmsEventsManager::hookAll('languages_forms', false, []);

        foreach ($languages_form_fields as $controller_name => $forms) {
            foreach($forms['forms'] as $form_name => $form){

                $name = implode(':', ['sources', $controller_name, $form_name]);

                $childs[] = new fieldCheckbox($name, [
                    'title' => $forms['title'].' / '.$form['title']
                ]);
            }
        }

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_LANGUAGES_CP_FORMS_OPT,
                'childs' => $childs
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_LANGUAGES_CP_SERVICES,
                'childs' => [
                    new fieldList('service', [
                        'items' => [
                            'google' => 'Google'
                        ]
                    ])
                ]
            ]
        ];
    }

}
