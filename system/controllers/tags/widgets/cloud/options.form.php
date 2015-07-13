<?php

class formWidgetTagsCloudOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('tags');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

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

                    new fieldNumber('options:limit', array(
                        'title' => LANG_WD_TAGS_CLOUD_LIMIT,
                        'default' => 10
                    )),

                )
            ),

        );

    }

}
