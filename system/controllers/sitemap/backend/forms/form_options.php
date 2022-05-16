<?php

class formSitemapOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $source_controllers = cmsEventsManager::hookAll('sitemap_sources');

        $childs = array();

        $changefreq = array(
            'always'  => LANG_SITEMAP_CHANGEFREQ1,
            'hourly'  => LANG_SITEMAP_CHANGEFREQ2,
            'daily'   => LANG_SITEMAP_CHANGEFREQ3,
            'weekly'  => LANG_SITEMAP_CHANGEFREQ4,
            'monthly' => LANG_SITEMAP_CHANGEFREQ5,
            'yearly'  => LANG_SITEMAP_CHANGEFREQ6,
            'never'   => LANG_SITEMAP_CHANGEFREQ7,
        );

        if (is_array($source_controllers)){
            foreach($source_controllers as $controller){
                foreach($controller['sources'] as $id => $title){

                    $childs[] = new fieldCheckbox("sources:{$controller['name']}|{$id}", array(
                        'title' => $title
                    ));

                    $childs[] = new fieldList("changefreq:{$controller['name']}:{$id}", array(
                        'default' => '',
                        'title'   => LANG_SITEMAP_CHANGEFREQ,
                        'items'   => (array('' => LANG_BY_DEFAULT) + $changefreq),
                        'visible_depend' => array("sources:{$controller['name']}|{$id}" => array('show' => array('1')))
                    ));

                    $childs[] = new fieldNumber("priority:{$controller['name']}:{$id}", array(
                        'title' => LANG_SITEMAP_PRIORITY,
                        'visible_depend' => array("sources:{$controller['name']}|{$id}" => array('show' => array('1'))),
                        'rules' => array(
                            array('min', 0),
                            array('max', 1)
                        )
                    ));

                }
            }
        }

        return array(

            'params' => array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('show_lastmod', array(
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_LASTMOD
                    )),
                    new fieldCheckbox('show_changefreq', array(
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_CHANGEFREQ
                    )),
                    new fieldList('default_changefreq', array(
                        'default' => 'daily',
                        'title' => LANG_SITEMAP_CHANGEFREQ.' '.mb_strtolower(LANG_BY_DEFAULT),
                        'items' => $changefreq,
                        'visible_depend' => array('show_changefreq' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('show_priority', array(
                        'default' => 1,
                        'title' => LANG_SITEMAP_SHOW_PRIORITY
                    )),
                    new fieldCheckbox('generate_html_sitemap', array(
                        'default' => 0,
                        'title' => LANG_SITEMAP_GENERATE_HTML_SITEMAP
                    )),
                    new fieldNumber('sitemap_items_count', array(
                        'default' => 50000,
                        'title' => LANG_SITEMAP_ITEMS_COUNT,
                        'hint' => LANG_SITEMAP_ITEMS_COUNT_HINT
                    ))
                )
            ),
            'sources' => array(
                'type' => 'fieldset',
                'title' => LANG_SITEMAP_SOURCES,
                'childs' => $childs
            ),
            'robots_txt' => array(
                'type' => 'fieldset',
                'title' => LANG_SITEMAP_ROBOTS_TXT,
                'childs' => array(
                    new fieldText('robots', array(
                        'default' => "User-agent: *\nDisallow:",
                        'hint' => LANG_SITEMAP_ROBOTS_TXT_HINT
                    ))
                )
            )

        );

    }

}
