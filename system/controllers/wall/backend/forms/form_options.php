<?php

class formWallOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type'  => 'fieldset',
                'title' => LANG_BASIC_OPTIONS,
                'childs' => array(

                    new fieldNumber('limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldNumber('show_entries', array(
                        'title' => LANG_WALL_SHOW_ENTRIES,
                        'default' => 5,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldList('order_by', array(
                        'title' => LANG_SORTING,
                        'default' => 'date_pub',
                        'rules' => array(
                            array('required')
                        ),
                        'items' => array(
                            'date_pub' => LANG_DATE_PUB,
                            'date_last_reply' => LANG_WALL_SORTING_DATE_LAST_REPLY
                        )
                    ))

                )
            )

        );

    }

}
