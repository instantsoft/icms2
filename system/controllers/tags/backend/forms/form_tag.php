<?php

class formTagsTag extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('tag', [
                        'title'   => LANG_TAGS_TAG,
                        'options' => [
                            'max_length' => 32
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldHtml('description', [
                        'title'   => LANG_TAGS_TAG_DESC,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'store_via_html_filter' => true
                    ]),
                    new fieldString('tag_title', [
                        'title'   => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => [
                            'max_length' => 300
                        ]
                    ]),
                    new fieldString('tag_desc', [
                        'title'   => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => [
                            'max_length' => 300
                        ]
                    ]),
                    new fieldString('tag_h1', [
                        'title'   => LANG_CP_SEOMETA_ITEM_H1,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => [
                            'max_length' => 300
                        ]
                    ])
                ]
            ]
        ];
    }

}
