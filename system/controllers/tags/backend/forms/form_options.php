<?php

class formTagsOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_TAGS_ROOT_OPTIONS,
                'childs' => array(
                    new fieldList('ordering', array(
                        'title' => LANG_WD_TAGS_CLOUD_ORDERING,
                        'items' => array(
                            'tag'       => LANG_WD_TAGS_CLOUD_ORDER_BY_TAG,
                            'frequency' => LANG_WD_TAGS_CLOUD_ORDER_BY_FREQ,
                        )
                    )),
                    new fieldList('style', array(
                        'title' => LANG_WD_TAGS_CLOUD_STYLE,
                        'items' => array(
                            'cloud' => LANG_WD_TAGS_CLOUD_STYLE_CLOUD,
                            'list'  => LANG_WD_TAGS_CLOUD_STYLE_LIST,
                        )
                    )),
                    new fieldNumber('max_fs', array(
                        'title'   => LANG_WD_TAGS_CLOUD_MAX_FS,
                        'default' => 22
                    )),
                    new fieldNumber('min_fs', array(
                        'title'   => LANG_WD_TAGS_CLOUD_MIN_FS,
                        'default' => 12
                    )),
                    new fieldNumber('min_freq', array(
                        'title'   => LANG_WD_TAGS_MIN_FREQ,
                        'default' => 0
                    )),
                    new fieldNumber('min_len', array(
                        'title'   => LANG_WD_TAGS_MIN_LEN,
                        'units'   => LANG_WD_TAGS_MIN_LEN_UNITS,
                        'default' => 0
                    )),
                    new fieldNumber('limit', array(
                        'title'   => LANG_WD_TAGS_CLOUD_LIMIT,
                        'default' => 10
                    )),
                    new fieldString('colors', array(
                        'title'   => LANG_WD_TAGS_COLORS,
                        'hint'    => LANG_WD_TAGS_COLORS_HINT,
                        'default' => ''
                    )),
                    new fieldCheckbox('shuffle', array(
                        'title' => LANG_WD_TAGS_SHUFFLE
                    ))
                )
            ),
            'seo_tags' => array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_SEOMETA_DEFAULT,
                'childs' => array(
                    new fieldString('seo_title_pattern', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    )),
                    new fieldString('seo_desc_pattern', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    )),
                    new fieldString('seo_h1_pattern', array(
                        'title'   => LANG_CP_SEOMETA_ITEM_H1,
                        'hint'    => LANG_TAGS_SEO_HINT,
                        'options' => array(
                            'max_length' => 300
                        )
                    ))
                )
            )
        );

    }

}
