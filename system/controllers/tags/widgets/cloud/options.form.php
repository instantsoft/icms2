<?php

class formWidgetTagsCloudOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('tags');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldListMultiple('options:subjects', array(
                        'title'     => LANG_WD_TAGS_SUBJECTS,
                        'show_all'  => true,
                        'generator' => function() {
                            $cts = cmsCore::getModel('content')->getContentTypes();
                            $items = array();
                            if ($cts) {
                                foreach ($cts as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }
                            return $items;
                        },
                    )),

                    new fieldList('options:ordering', array(
                        'title' => LANG_WD_TAGS_CLOUD_ORDERING,
                        'items' => array(
                            'tag' => LANG_WD_TAGS_CLOUD_ORDER_BY_TAG,
                            'frequency' => LANG_WD_TAGS_CLOUD_ORDER_BY_FREQ,
                        )
                    )),

                    new fieldList('options:style', array(
                        'title' => LANG_WD_TAGS_CLOUD_STYLE,
                        'items' => array(
                            'cloud' => LANG_WD_TAGS_CLOUD_STYLE_CLOUD,
                            'list' => LANG_WD_TAGS_CLOUD_STYLE_LIST,
                        )
                    )),

                    new fieldNumber('options:max_fs', array(
                        'title' => LANG_WD_TAGS_CLOUD_MAX_FS,
                        'default' => 22
                    )),

                    new fieldNumber('options:min_fs', array(
                        'title' => LANG_WD_TAGS_CLOUD_MIN_FS,
                        'default' => 12
                    )),

                    new fieldNumber('options:min_freq', array(
                        'title' => LANG_WD_TAGS_MIN_FREQ,
                        'default' => 0
                    )),

                    new fieldNumber('options:min_len', array(
                        'title' => LANG_WD_TAGS_MIN_LEN,
                        'units' => LANG_WD_TAGS_MIN_LEN_UNITS,
                        'default' => 0
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_WD_TAGS_CLOUD_LIMIT,
                        'default' => 10
                    )),

                    new fieldString('options:colors', array(
                        'title' => LANG_WD_TAGS_COLORS,
                        'hint'  => LANG_WD_TAGS_COLORS_HINT,
                        'default' => ''
                    )),

                    new fieldCheckbox('options:shuffle', array(
                        'title' => LANG_WD_TAGS_SHUFFLE
                    ))

                )
            ),

        );

    }

}
