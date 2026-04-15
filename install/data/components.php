<?php
/**
 * Данные компонентов для выборочной установки
 */
return [
    'mandatory' => [
        'admin', 'content', 'users', 'messages', 'auth',
        'moderation', 'images', 'wysiwygs', 'languages', 'typograph'
    ],

    'categories' => [
        'social' => [
            'title'     => 'LANG_CAT_SOCIAL',
            'components' => [
                'comments' => [
                    'title' => 'LANG_COMMENTS',
                    'desc'  => 'LANG_COMMENTS_DESC',
                    'deps'  => []
                ],
                'rating'  => [
                    'title' => 'LANG_RATING',
                    'desc'  => 'LANG_RATING_DESC',
                    'deps'  => []
                ],
                'activity' => [
                    'title' => 'LANG_ACTIVITY',
                    'desc'  => 'LANG_ACTIVITY_DESC',
                    'deps'  => ['comments', 'rating']
                ],
                'wall'    => [
                    'title' => 'LANG_WALL',
                    'desc'  => 'LANG_WALL_DESC',
                    'deps'  => ['comments', 'activity']
                ],
                'groups'  => [
                    'title' => 'LANG_GROUPS',
                    'desc'  => 'LANG_GROUPS_DESC',
                    'deps'  => ['wall', 'activity']
                ],
            ]
        ],
        'content' => [
            'title'     => 'LANG_CAT_CONTENT',
            'components' => [
                'tags'    => [
                    'title' => 'LANG_TAGS',
                    'desc'  => 'LANG_TAGS_DESC',
                    'deps'  => []
                ],
                'search'  => [
                    'title' => 'LANG_SEARCH',
                    'desc'  => 'LANG_SEARCH_DESC',
                    'deps'  => []
                ],
                'sitemap' => [
                    'title' => 'LANG_SITEMAP',
                    'desc'  => 'LANG_SITEMAP_DESC',
                    'deps'  => []
                ],
                'rss'     => [
                    'title' => 'LANG_RSS',
                    'desc'  => 'LANG_RSS_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'media' => [
            'title'     => 'LANG_CAT_MEDIA',
            'components' => [
                'photos' => [
                    'title' => 'LANG_PHOTOS',
                    'desc'  => 'LANG_PHOTOS_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'tools' => [
            'title'     => 'LANG_CAT_TOOLS',
            'components' => [
                'forms' => [
                    'title' => 'LANG_FORMS',
                    'desc'  => 'LANG_FORMS_DESC',
                    'deps'  => []
                ],
                'geo'   => [
                    'title' => 'LANG_GEO',
                    'desc'  => 'LANG_GEO_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'security' => [
            'title'     => 'LANG_CAT_SECURITY',
            'components' => [
                'recaptcha' => [
                    'title' => 'LANG_RECAPTCHA',
                    'desc'  => 'LANG_RECAPTCHA_DESC',
                    'deps'  => []
                ],
                'csp'      => [
                    'title' => 'LANG_CSP',
                    'desc'  => 'LANG_CSP_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'monetization' => [
            'title'     => 'LANG_CAT_MONETIZATION',
            'components' => [
                'billing' => [
                    'title' => 'LANG_BILLING',
                    'desc'  => 'LANG_BILLING_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'seo' => [
            'title'     => 'LANG_CAT_SEO',
            'components' => [
                'redirect' => [
                    'title' => 'LANG_REDIRECT',
                    'desc'  => 'LANG_REDIRECT_DESC',
                    'deps'  => []
                ],
            ]
        ],
        'notifications' => [
            'title'     => 'LANG_CAT_NOTIFICATIONS',
            'components' => [
                'subscriptions' => [
                    'title' => 'LANG_SUBSCRIPTIONS',
                    'desc'  => 'LANG_SUBSCRIPTIONS_DESC',
                    'deps'  => []
                ],
            ]
        ],
    ],

    'install_types' => [
        'minimal' => [
            'title'       => 'LANG_INSTALL_MINIMAL',
            'desc'        => 'LANG_INSTALL_MINIMAL_DESC',
            'components'  => [],
            'demo'        => false
        ],
        'standard' => [
            'title'       => 'LANG_INSTALL_STANDARD',
            'desc'        => 'LANG_INSTALL_STANDARD_DESC',
            'components'  => ['comments', 'rating', 'activity', 'wall', 'groups', 'tags', 'search', 'photos'],
            'demo'        => 'standard'
        ],
        'full' => [
            'title'       => 'LANG_INSTALL_FULL',
            'desc'        => 'LANG_INSTALL_FULL_DESC',
            'components'  => ['comments', 'rating', 'activity', 'wall', 'groups', 'tags', 'search', 'sitemap', 'rss', 'photos', 'forms', 'geo', 'recaptcha', 'csp', 'billing', 'subscriptions', 'redirect'],
            'demo'        => 'full'
        ],
        'custom' => [
            'title'       => 'LANG_INSTALL_CUSTOM',
            'desc'        => 'LANG_INSTALL_CUSTOM_DESC',
            'components'  => [],
            'demo'        => true
        ]
    ],

    'dependencies' => [
        'photos'    => [],
        'comments'  => [],
        'rating'    => [],
        'activity'  => ['comments', 'rating'],
        'wall'      => ['comments', 'activity'],
        'groups'    => ['wall', 'activity'],
        'tags'      => [],
        'search'    => [],
        'sitemap'   => [],
        'rss'       => [],
        'photos'    => [],
        'forms'     => [],
        'geo'       => [],
        'recaptcha' => [],
        'csp'       => [],
        'billing'   => [],
        'redirect'  => [],
        'subscriptions' => [],
    ]
];
