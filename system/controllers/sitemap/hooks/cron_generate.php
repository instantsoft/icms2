<?php

class onSitemapCronGenerate extends cmsAction {

    public $disallow_event_db_register = true;

    private $max_count      = 50000;
    private $max_html_count = 500;

    public function run() {

        $sources_list = $this->options['sources'];
        if (!$sources_list) {
            return false;
        }

        $priority_list   = !empty($this->options['priority']) ? $this->options['priority'] : [];
        $changefreq_list = !empty($this->options['changefreq']) ? $this->options['changefreq'] : [];

        if (!is_writable($this->cms_config->root_path . 'cache/static/sitemaps/')) {
            return false;
        }

        if(!empty($this->options['sitemap_items_count'])){
            $this->max_count = $this->options['sitemap_items_count'];
        }

        $source_controllers = [];
        $sources            = [];
        $sitemaps           = [];
        $sitemaps_html      = [];

        if (!empty($this->options['generate_html_sitemap'])) {
            $source_controllers = cmsEventsManager::hookAll('sitemap_sources');
        }

        foreach ($sources_list as $item => $is_enabled) {

            if (!$is_enabled) {
                continue;
            }

            $targets = explode('|', $item);

            if (count($targets) == 2) {

                list($controller_name, $source) = $targets;

            } else {

                $controller_name = array_shift($targets);

                $source = $targets;
            }

            $sources[$controller_name][] = $source;
        }

        foreach ($sources as $controller_name => $items) {

            $urls = [];

            $controller = cmsCore::getController($controller_name);

            foreach ($items as $item) {

                $urls = $controller->runHook('sitemap_urls', [$item]);
                if (!$urls) {
                    continue;
                }

                if (!is_array($item)) {
                    $item = [$item];
                }

                list($item, $urls) = cmsEventsManager::hook('sitemap_urls_list_'.$controller_name, [$item, $urls]);

                $source_key = implode('|', $item);

                $sitemap_file = "sitemap_{$controller_name}_" . implode('_', $item) . "%s";

                $changefreq = $this->options['default_changefreq'];
                $priority   = null;

                if (!empty($changefreq_list[$controller_name][$source_key])) {

                    $changefreq = $changefreq_list[$controller_name][$source_key];
                }

                if (!empty($priority_list[$controller_name][$source_key])) {

                    $priority = $priority_list[$controller_name][$source_key];
                }

                // если есть отдельный шаблон, используем его
                $template_file = 'sitemap_' . $controller_name . '_' . implode('_', $item);
                if (!$this->cms_template->getTemplateFileName('controllers/sitemap/' . $template_file, true)) {
                    $template_file = 'sitemap';
                }

                // sitemap.xml
                if (count($urls) > $this->max_count) {

                    $chunk_data = array_chunk($urls, $this->max_count, true);

                    foreach ($chunk_data as $index => $chunk_urls) {

                        $index = $index ? '_' . $index : '';

                        $sitemap_file_xml = sprintf($sitemap_file, $index . '.xml');

                        file_put_contents(
                            $this->cms_config->root_path . "cache/static/sitemaps/{$sitemap_file_xml}",
                            $this->cms_template->renderInternal($this, $template_file, [
                                'urls'            => $chunk_urls,
                                'changefreq'      => $changefreq,
                                'priority'        => $priority,
                                'show_lastmod'    => !empty($this->options['show_lastmod']),
                                'show_changefreq' => !empty($this->options['show_changefreq']),
                                'show_priority'   => !empty($this->options['show_priority'])
                            ])
                        );

                        $sitemaps[] = $sitemap_file_xml;
                    }

                    unset($chunk_data, $chunk_urls);

                } else {

                    $sitemap_file_xml = sprintf($sitemap_file, '.xml');

                    file_put_contents(
                        $this->cms_config->root_path . "cache/static/sitemaps/{$sitemap_file_xml}",
                        $this->cms_template->renderInternal($this, $template_file, [
                            'urls'            => $urls,
                            'changefreq'      => $changefreq,
                            'priority'        => $priority,
                            'show_lastmod'    => !empty($this->options['show_lastmod']),
                            'show_changefreq' => !empty($this->options['show_changefreq']),
                            'show_priority'   => !empty($this->options['show_priority'])
                        ])
                    );

                    $sitemaps[] = $sitemap_file_xml;
                }

                if (!$source_controllers) {
                    continue;
                }

                // sitemap.html
                if (count($urls) > $this->max_html_count) {

                    $chunk_data = array_chunk($urls, $this->max_html_count, true);
                    unset($urls);

                    foreach ($chunk_data as $index => $chunk_urls) {

                        $number = $index + 1;

                        $index = $index ? '_' . $index : '';

                        $sitemap_file_html = sprintf($sitemap_file, $index);

                        file_put_contents(
                            $this->cms_config->root_path . 'cache/static/sitemaps/' . $sitemap_file_html . '.json',
                            json_encode($chunk_urls, JSON_UNESCAPED_UNICODE)
                        );

                        $sitemaps_html[] = [
                            'url'   => href_to_abs('sitemap', $sitemap_file_html),
                            'title' => $source_controllers[$controller_name]['sources'][$source_key] . ', ' . mb_strtolower(LANG_PAGE) . $number
                        ];
                    }
                } else {

                    $sitemap_file_html = sprintf($sitemap_file, '');

                    file_put_contents(
                        $this->cms_config->root_path . 'cache/static/sitemaps/' . $sitemap_file_html . '.json',
                        json_encode($urls, JSON_UNESCAPED_UNICODE)
                    );

                    $sitemaps_html[] = [
                        'url'   => href_to_abs('sitemap', $sitemap_file_html),
                        'title' => $source_controllers[$controller_name]['sources'][$source_key]
                    ];
                }
            }
        }

        file_put_contents(
            $this->cms_config->root_path . 'cache/static/sitemaps/sitemap.xml',
            $this->cms_template->renderInternal($this, 'sitemap_index', [
                'sitemaps' => $sitemaps,
                'host'     => $this->cms_config->host
            ])
        );

        if ($sitemaps_html) {

            file_put_contents(
                $this->cms_config->root_path . 'cache/static/sitemaps/sitemap.json',
                json_encode($sitemaps_html, JSON_UNESCAPED_UNICODE)
            );
        }

        return true;
    }

}
