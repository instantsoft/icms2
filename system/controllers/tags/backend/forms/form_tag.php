<?php

class formTagsTag extends cmsForm {

    public function init() {

        return array(
            'basic' => array(
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldString('tag', array(
                        'title'   => LANG_TAGS_TAG,
                        'options' => array(
                            'max_length' => 32
                        ),
                        'rules'   => array(
                            array('required')
                        )
                    )),
                    new fieldString('tag_title', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    )),
                    new fieldString('tag_desc', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    )),
                    new fieldString('tag_h1', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_H1,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    ))
                )
            ),
        );

    }

}
