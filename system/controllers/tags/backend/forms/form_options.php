<?php

class formTagsOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_TAGS_ROOT_OPTIONS,
                'childs' => [
                    new fieldList('ordering', [
                        'title' => LANG_WD_TAGS_CLOUD_ORDERING,
                        'items' => [
                            'tag'       => LANG_WD_TAGS_CLOUD_ORDER_BY_TAG,
                            'frequency' => LANG_WD_TAGS_CLOUD_ORDER_BY_FREQ,
                        ]
                    ]),
                    new fieldList('style', [
                        'title' => LANG_WD_TAGS_CLOUD_STYLE,
                        'items' => [
                            'cloud' => LANG_WD_TAGS_CLOUD_STYLE_CLOUD,
                            'list'  => LANG_WD_TAGS_CLOUD_STYLE_LIST,
                        ]
                    ]),
                    new fieldNumber('max_fs', [
                        'title'   => LANG_WD_TAGS_CLOUD_MAX_FS,
                        'default' => 22
                    ]),
                    new fieldNumber('min_fs', [
                        'title'   => LANG_WD_TAGS_CLOUD_MIN_FS,
                        'default' => 12
                    ]),
                    new fieldNumber('min_freq', [
                        'title'   => LANG_WD_TAGS_MIN_FREQ,
                        'default' => 0
                    ]),
                    new fieldNumber('min_len', [
                        'title'   => LANG_WD_TAGS_MIN_LEN,
                        'units'   => LANG_WD_TAGS_MIN_LEN_UNITS,
                        'default' => 0
                    ]),
                    new fieldNumber('limit', [
                        'title'   => LANG_WD_TAGS_CLOUD_LIMIT,
                        'default' => 10
                    ]),
                    new fieldString('colors', [
                        'title'   => LANG_WD_TAGS_COLORS,
                        'hint'    => LANG_WD_TAGS_COLORS_HINT,
                        'default' => ''
                    ]),
                    new fieldCheckbox('shuffle', [
                        'title' => LANG_WD_TAGS_SHUFFLE
                    ])
                ]
            ],
            'seo_tags' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SEOMETA_DEFAULT,
                'childs' => [
                    new fieldString('seo_title_pattern', [
                        'title'   => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => [
                            'max_length' => 300
                        ]
                    ]),
                    new fieldString('seo_desc_pattern', [
                        'title'   => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => [
                            'max_length' => 300
                        ]
                    ]),
                    new fieldString('seo_h1_pattern', [
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
