<?php

class formSitemapOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $source_controllers = cmsEventsManager::hookAll('sitemap_sources');

        $childs = [];

        $changefreq = [
            'always'  => LANG_SITEMAP_CHANGEFREQ1,
            'hourly'  => LANG_SITEMAP_CHANGEFREQ2,
            'daily'   => LANG_SITEMAP_CHANGEFREQ3,
            'weekly'  => LANG_SITEMAP_CHANGEFREQ4,
            'monthly' => LANG_SITEMAP_CHANGEFREQ5,
            'yearly'  => LANG_SITEMAP_CHANGEFREQ6,
            'never'   => LANG_SITEMAP_CHANGEFREQ7
        ];

        if (is_array($source_controllers)) {
            foreach ($source_controllers as $controller) {
                foreach ($controller['sources'] as $id => $title) {

                    $childs[] = new fieldCheckbox("sources:{$controller['name']}|{$id}", [
                        'title' => $title
                    ]);

                    $childs[] = new fieldList("changefreq:{$controller['name']}:{$id}", [
                        'default'        => '',
                        'title'          => LANG_SITEMAP_CHANGEFREQ,
                        'items'          => (['' => LANG_BY_DEFAULT] + $changefreq),
                        'visible_depend' => ["sources:{$controller['name']}|{$id}" => ['show' => ['1']]]
                    ]);

                    $childs[] = new fieldNumber("priority:{$controller['name']}:{$id}", [
                        'title'          => LANG_SITEMAP_PRIORITY,
                        'visible_depend' => ["sources:{$controller['name']}|{$id}" => ['show' => ['1']]],
                        'rules'          => [
                            ['min', 0],
                            ['max', 1]
                        ]
                    ]);
                }
            }
        }

        return [
            'params' => [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('show_lastmod', [
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_LASTMOD
                    ]),
                    new fieldCheckbox('show_changefreq', [
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_CHANGEFREQ
                    ]),
                    new fieldList('default_changefreq', [
                        'default' => 'daily',
                        'title' => LANG_SITEMAP_CHANGEFREQ.' '.mb_strtolower(LANG_BY_DEFAULT),
                        'items' => $changefreq,
                        'visible_depend' => ['show_changefreq' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('show_priority', [
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_PRIORITY
                    ]),
                    new fieldCheckbox('generate_html_sitemap', [
                        'default' => 0,
                        'title' => LANG_SITEMAP_GENERATE_HTML_SITEMAP
                    ]),
                    new fieldNumber('sitemap_items_count', [
                        'default' => 50000,
                        'title' => LANG_SITEMAP_ITEMS_COUNT,
                        'hint' => LANG_SITEMAP_ITEMS_COUNT_HINT
                    ])
                ]
            ],
            'sources' => [
                'type' => 'fieldset',
                'title' => LANG_SITEMAP_SOURCES,
                'childs' => $childs
            ],
            'robots_txt' => [
                'type' => 'fieldset',
                'title' => LANG_SITEMAP_ROBOTS_TXT,
                'childs' => [
                    new fieldText('robots', [
                        'default' => "User-agent: *\nDisallow:",
                        'hint' => LANG_SITEMAP_ROBOTS_TXT_HINT
                    ])
                ]
            ]
        ];
    }

}
