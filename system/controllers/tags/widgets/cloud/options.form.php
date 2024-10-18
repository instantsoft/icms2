<?php

class formWidgetTagsCloudOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('tags');

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldListMultiple('options:subjects', [
                        'title'     => LANG_WD_TAGS_SUBJECTS,
                        'show_all'  => true,
                        'generator' => function () {
                            $cts   = cmsCore::getModel('content')->getContentTypes();
                            $items = [];
                            if ($cts) {
                                foreach ($cts as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }
                            return $items;
                        }
                    ]),
                    new fieldList('options:ordering', [
                        'title' => LANG_WD_TAGS_CLOUD_ORDERING,
                        'items' => [
                            'tag'       => LANG_WD_TAGS_CLOUD_ORDER_BY_TAG,
                            'frequency' => LANG_WD_TAGS_CLOUD_ORDER_BY_FREQ,
                        ]
                    ]),
                    new fieldList('options:style', [
                        'title' => LANG_WD_TAGS_CLOUD_STYLE,
                        'items' => [
                            'cloud' => LANG_WD_TAGS_CLOUD_STYLE_CLOUD,
                            'list'  => LANG_WD_TAGS_CLOUD_STYLE_LIST,
                        ]
                    ]),
                    new fieldNumber('options:max_fs', [
                        'title'   => LANG_WD_TAGS_CLOUD_MAX_FS,
                        'default' => 22
                    ]),
                    new fieldNumber('options:min_fs', [
                        'title'   => LANG_WD_TAGS_CLOUD_MIN_FS,
                        'default' => 12
                    ]),
                    new fieldNumber('options:min_freq', [
                        'title'   => LANG_WD_TAGS_MIN_FREQ,
                        'default' => 0
                    ]),
                    new fieldNumber('options:min_len', [
                        'title'   => LANG_WD_TAGS_MIN_LEN,
                        'units'   => LANG_WD_TAGS_MIN_LEN_UNITS,
                        'default' => 0
                    ]),
                    new fieldNumber('options:limit', [
                        'title'   => LANG_WD_TAGS_CLOUD_LIMIT,
                        'default' => 10
                    ]),
                    new fieldString('options:colors', [
                        'title'   => LANG_WD_TAGS_COLORS,
                        'hint'    => LANG_WD_TAGS_COLORS_HINT,
                        'default' => ''
                    ]),
                    new fieldCheckbox('options:shuffle', [
                        'title' => LANG_WD_TAGS_SHUFFLE
                    ])
                ]
            ]
        ];
    }

}
