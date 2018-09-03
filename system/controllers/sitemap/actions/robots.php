<?php

class actionSitemapRobots extends cmsAction {

    public function run(){

        header('Content-Disposition: inline; filename="robots.txt"');
        header('Content-type: text/plain');

        echo str_replace("\r\n", "\n", trim($this->options['robots']))."\n";

        $host = $this->cms_config->host;

        if($this->cms_config->protocol === 'http://'){
            $host = parse_url($this->cms_config->host, PHP_URL_HOST);
        }

        echo "Host: {$host}\n";
        echo "Sitemap: ".href_to_home(true)."sitemap.xml\n";

        exit;

    }

}
