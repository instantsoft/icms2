<?php

class formTypographPreset extends cmsForm {

    public function init($do, $html_tags) {

        return [
            'basic' => [
                'title'  => LANG_BASIC_OPTIONS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_TYP_PRESET_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldCheckbox('options:is_auto_br', [
                        'title' => LANG_TYP_IS_AUTO_BR
                    ]),
                    new fieldCheckbox('options:is_auto_link_mode', [
                        'title' => LANG_TYP_IS_AUTO_LINK_MODE
                    ]),
                    new fieldCheckbox('options:build_redirect_link', [
                        'title' => LANG_TYP_BUILD_REDIRECT_LINK
                    ]),
                    new fieldCheckbox('options:build_smiles', [
                        'title' => LANG_TYP_BUILD_SMILES,
                        'hint' => LANG_TYP_BUILD_SMILES_HINT
                    ]),
                    new fieldCheckbox('options:is_process_callback', [
                        'title' => LANG_TYP_IS_PROCESS_CALLBACK
                    ]),
                    new fieldFieldsgroup('options:autoreplace', [
                        'title' => LANG_TYP_AUTOREPLACE,
                        'childs' => [
                            new fieldString('search', [
                                'attributes' => ['placeholder' => LANG_TYP_AUTOREPLACE_FROM],
                                'rules' => [
                                    ['required']
                                ]
                            ]),
                            new fieldString('replace', [
                                'attributes' => ['placeholder' => LANG_TYP_AUTOREPLACE_TO]
                            ])
                        ]
                    ]),
                    new fieldList('options:allowed_tags', [
                        'title' => LANG_TYP_ALLOWED_TAGS,
                        'is_chosen_multiple' => true,
                        'items' => array_combine($html_tags, $html_tags)
                    ])
                ]
            ]
        ];
    }
}
