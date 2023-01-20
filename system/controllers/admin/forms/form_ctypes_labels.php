<?php

class formAdminCtypesLabels extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_NUMERALS_LABELS,
                'can_multilanguage' => true,
                'childs' => [
                    new fieldString('labels:one', [
                        'title' => LANG_CP_NUMERALS_1_LABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:two', [
                        'title' => LANG_CP_NUMERALS_2_LABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:many', [
                        'title' => LANG_CP_NUMERALS_10_LABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:one_genitive', [
                        'title' => LANG_CP_NUMERALS_1_GLABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:two_genitive', [
                        'title' => LANG_CP_NUMERALS_2_GLABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:many_genitive', [
                        'title' => LANG_CP_NUMERALS_10_GLABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:one_accusative', [
                        'title' => LANG_CP_NUMERALS_1_ALABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:two_accusative', [
                        'title' => LANG_CP_NUMERALS_2_ALABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:many_accusative', [
                        'title' => LANG_CP_NUMERALS_10_ALABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_ACTIONS_LABELS,
                'can_multilanguage' => true,
                'childs' => [
                    new fieldString('labels:create', [
                        'title' => LANG_CP_ACTION_ADD_LABEL,
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_LIST_LABELS,
                'can_multilanguage' => true,
                'childs' => [
                    new fieldString('labels:list', [
                        'title' => LANG_CP_LIST_LABEL,
                        'hint'  => LANG_CP_LIST_LABELS_HINT,
                        'rules' => [
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('labels:profile', [
                        'title' => LANG_CP_PROFILE_LABEL,
                        'hint'  => LANG_CP_LIST_LABELS_HINT,
                        'rules' => [
                            ['max_length', 100]
                        ]
                    ])
                ]
            ],
            'ctype_relations' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_CTYPE_RELATIONS,
                'can_multilanguage' => true,
                'childs' => [
                    new fieldString('labels:relations_tab_title', [
                        'title' => LANG_CP_LIST_LABELS_RTAB_TITLE,
                        'hint'  => LANG_CP_LIST_LABELS_RTAB_TITLE_HINT,
                        'rules' => [
                            ['max_length', 100]
                        ]
                    ])
                ]
            ]
        ];
    }

}
