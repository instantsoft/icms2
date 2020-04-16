<?php

class formRssFeed extends cmsForm {

    public function init() {

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
                            array('digits'),
                            array('min', 1),
                            array('max', 50),
                        )
                    )),

                    new fieldList('template', array(
                        'title' => LANG_RSS_FEED_TEMPLATE,
                        'hint'  => LANG_RSS_FEED_TEMPLATE_HINT,
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/rss', '*.tpl.php');
                        }
                    ))

                )
            ),

            'image' => array(
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_IMAGE,
                'childs' => array(
                    new fieldImage('image', array())
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
							array('digits'),
							array('min', 1)
						)
                    ))

                )
            )

        );

    }

}
