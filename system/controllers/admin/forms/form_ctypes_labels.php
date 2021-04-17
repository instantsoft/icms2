<?php

class formAdminCtypesLabels extends cmsForm {

    public function init() {

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_NUMERALS_LABELS,
                'childs' => array(
                    new fieldString('labels:one', array(
                        'title' => LANG_CP_NUMERALS_1_LABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:two', array(
                        'title' => LANG_CP_NUMERALS_2_LABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:many', array(
                        'title' => LANG_CP_NUMERALS_10_LABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:one_genitive', array(
                        'title' => LANG_CP_NUMERALS_1_GLABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:two_genitive', array(
                        'title' => LANG_CP_NUMERALS_2_GLABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:many_genitive', array(
                        'title' => LANG_CP_NUMERALS_10_GLABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:one_accusative', array(
                        'title' => LANG_CP_NUMERALS_1_ALABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:two_accusative', array(
                        'title' => LANG_CP_NUMERALS_2_ALABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:many_accusative', array(
                        'title' => LANG_CP_NUMERALS_10_ALABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    ))
                )
            ),
            array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_ACTIONS_LABELS,
                'childs' => array(
                    new fieldString('labels:create', array(
                        'title' => LANG_CP_ACTION_ADD_LABEL,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                )
            ),
            array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_LIST_LABELS,
                'childs' => array(
                    new fieldString('labels:list', array(
                        'title' => LANG_CP_LIST_LABEL,
                        'hint'  => LANG_CP_LIST_LABELS_HINT,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:profile', array(
                        'title' => LANG_CP_PROFILE_LABEL,
                        'hint'  => LANG_CP_LIST_LABELS_HINT,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    )),
                )
            ),
            'ctype_relations' => array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_CTYPE_RELATIONS,
                'childs' => array(
                    new fieldString('labels:relations_tab_title', array(
                        'title' => LANG_CP_LIST_LABELS_RTAB_TITLE,
                        'hint'  => LANG_CP_LIST_LABELS_RTAB_TITLE_HINT,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    ))
                )
            )
        );

    }

}
