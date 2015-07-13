<?php

class formRssFeed extends cmsForm {

    public function init($ctype_fields) {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_BASIC,
                'childs' => array(

                    new fieldCheckbox('is_enabled', array(
                        'title' => LANG_RSS_FEED_ENABLED
                    )),

                    new fieldString('description', array(
                        'title' => LANG_RSS_FEED_DESC,
                    )),

                    new fieldNumber('limit', array(
                        'title' => LANG_RSS_FEED_LIMIT,
                        'rules' => array(
                            array('required'),
                            array('min', 0),
                            array('max', 50),
                        )
                    )),

                )
            ),

            'image' => array(
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_IMAGE,
                'childs' => array(

                    new fieldImage('image', array(
                    )),

                )
            ),

            'cache' => array(
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_CACHING,
                'childs' => array(

                    new fieldCheckbox('is_cache', array(
                        'title' => LANG_RSS_FEED_CACHE
                    )),

                    new fieldNumber('cache_interval', array(
                        'title' => LANG_RSS_FEED_CACHE_INT,
                        'rules' => array(
                            array('min', 0),
                        )
                    )),

                )
            ),

            'mapping' => array(
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_MAPPING,
                'childs' => array(

                    new fieldList('mapping:title', array(
                        'title' => LANG_RSS_FEED_MAP_TITLE,
                        'items' => $ctype_fields
                    )),

                    new fieldList('mapping:description', array(
                        'title' => LANG_RSS_FEED_MAP_DESC,
                        'items' => $ctype_fields
                    )),

                    new fieldList('mapping:pubDate', array(
                        'title' => LANG_RSS_FEED_MAP_DATE,
                        'items' => $ctype_fields
                    )),

                    new fieldList('mapping:image', array(
                        'title' => LANG_RSS_FEED_MAP_IMAGE,
                        'items' => $ctype_fields
                    )),

                    new fieldList('mapping:image_size', array(
                        'title' => LANG_RSS_FEED_MAP_IMAGE_SIZE,
                        'items' => array(
                            'micro' => LANG_PARSER_IMAGE_SIZE_MICRO,
                            'small' => LANG_PARSER_IMAGE_SIZE_SMALL,
                            'normal' => LANG_PARSER_IMAGE_SIZE_NORMAL,
                            'big' => LANG_PARSER_IMAGE_SIZE_BIG,
                            'original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL
                        )
                    )),

                )
            ),

        );

    }

}