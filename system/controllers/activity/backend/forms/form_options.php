<?php

class formActivityOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_BASIC_OPTIONS,

                'childs' => [
                    new fieldList('types', [
                        'title'                    => LANG_ACTIVITY_OPT_TYPES,
                        'is_multiple'              => true,
                        'multiple_select_deselect' => true,
                        'generator' => function () {
                            $types = cmsCore::getModel('activity')->getTypes();
                            return array_collection_to_list($types, 'id', 'title');
                        }
                    ]),
                    new fieldNumber('subject_max_title_len', [
                        'title'   => LANG_ACTIVITY_TITLE_LEN,
                        'default' => 50,
                        'rules'   => [
                            ['max', 140] // subject_title	varchar(140)
                        ]
                    ]),
                    new fieldNumber('limit', [
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];
    }

}
