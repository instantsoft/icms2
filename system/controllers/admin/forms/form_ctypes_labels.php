<?php
class formAdminCtypesLabels extends cmsForm {

    public function init() {

        return array(
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_NUMERALS_LABELS,
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
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_ACTIONS_LABELS,
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
                'type' => 'fieldset',
                'title' => LANG_CP_LIST_LABELS,
                'childs' => array(
                    new fieldString('labels:list', array(
                        'title' => LANG_CP_LIST_LABEL,
                        'hint' => LANG_CP_LIST_LABELS_HINT,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('labels:profile', array(
                        'title' => LANG_CP_PROFILE_LABEL,
                        'hint' => LANG_CP_LIST_LABELS_HINT,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    )),
                )
            ),
        );

    }

}