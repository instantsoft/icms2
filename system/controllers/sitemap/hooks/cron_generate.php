<?php

class onSitemapCronGenerate extends cmsAction {

    // вообще ограничение 50000 или 10Мб, чтобы не проверять размер файла, задаем меньше
    private $max_count = 45000;

    public function run(){

        // автоматическое получение опций через $this->options здесь не
        // работает, потому что форма опций не содержит полей, они заполняются
        // динамически в админке
        $options = $this->loadOptions($this->name);
        if (!$options) { return false; }

        $sources_list = $options['sources'];
        if (!$sources_list) { return false; }

        $config = cmsConfig::getInstance();

        if(!is_writable($config->root_path.'cache/static/sitemaps/')){
            return false;
        }

        $sources = array();
        $sitemaps = array();

        foreach($sources_list as $item=>$is_enabled){
            if (!$is_enabled) { continue; }
            list($controller_name, $source) = explode('|', $item);
            $sources[$controller_name][] = $source;
        }

        foreach($sources as $controller_name => $items){

            $urls = array();

            $controller = cmsCore::getController($controller_name);

            foreach($items as $item){

                $urls = $controller->runHook('sitemap_urls', array($item));
				if (!$urls) { continue; }

                if(count($urls)>$this->max_count){

                    $chunk_data = array_chunk($urls, $this->max_count, true);

                    foreach ($chunk_data as $index=>$chunk_urls) {

                        $index = $index ? '_'.$index : '';

                        $xml = cmsTemplate::getInstance()->renderInternal($this, 'sitemap', array(
                            'urls' => $chunk_urls
                        ));

                        $sitemap_file = "sitemap_{$controller_name}_{$item}{$index}.xml";

                        file_put_contents($config->root_path . "cache/static/sitemaps/{$sitemap_file}", $xml);

                        $sitemaps[] = $sitemap_file;

                    }

                } else {

                    $xml = cmsTemplate::getInstance()->renderInternal($this, 'sitemap', array(
                        'urls' => $urls
                    ));

                    $sitemap_file = "sitemap_{$controller_name}_{$item}.xml";

                    file_put_contents($config->root_path . "cache/static/sitemaps/{$sitemap_file}", $xml);

                    $sitemaps[] = $sitemap_file;

                }

            }

        }

        $xml = cmsTemplate::getInstance()->renderInternal($this, 'sitemap_index', array(
            'sitemaps' => $sitemaps,
            'host' => $config->host
        ));

        file_put_contents(cmsConfig::get('root_path') . 'cache/static/sitemaps/sitemap.xml', $xml);

        return true;

    }

}