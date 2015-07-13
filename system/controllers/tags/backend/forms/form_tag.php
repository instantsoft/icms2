<?php

class formTagsTag extends cmsForm {

    public function init() {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('tag', array(
                        'title' => LANG_TAGS_TAG,
                        'rules' => array(
                            array('required'),
                            array('max_length', 32)
                        )
                    )),

                )
            ),

        );

    }

}