<?php

class formRssFeed extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_BASIC,
                'childs' => [
                    new fieldCheckbox('is_enabled', [
                        'title' => LANG_RSS_FEED_ENABLED
                    ]),
                    new fieldString('description', [
                        'title' => LANG_RSS_FEED_DESC,
                    ]),
                    new fieldNumber('limit', [
                        'title' => LANG_RSS_FEED_LIMIT,
                        'rules' => [
                            ['required'],
                            ['digits'],
                            ['min', 1],
                            ['max', 50],
                        ]
                    ]),
                    new fieldList('template', [
                        'title' => LANG_RSS_FEED_TEMPLATE,
                        'hint'  => LANG_RSS_FEED_TEMPLATE_HINT,
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/rss', '*.tpl.php');
                        }
                    ])
                ]
            ],
            'image' => [
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_IMAGE,
                'childs' => [
                    new fieldImage('image', [])
                ]
            ],
            'cache' => [
                'type' => 'fieldset',
                'title' => LANG_RSS_FEED_CACHING,
                'childs' => [
                    new fieldCheckbox('is_cache', [
                        'title' => LANG_RSS_FEED_CACHE
                    ]),
                    new fieldNumber('cache_interval', [
                        'title' => LANG_RSS_FEED_CACHE_INT,
                        'rules' => [
                            ['digits'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];
    }

}
