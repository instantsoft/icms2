<?php

class formWidgetCommentsListOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldCheckbox('options:show_avatars', array(
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_AVATARS,
                        'default' => true,
                    )),

                    new fieldCheckbox('options:show_text', array(
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_TEXT,
                        'default' => false,
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),

        );

    }

}
