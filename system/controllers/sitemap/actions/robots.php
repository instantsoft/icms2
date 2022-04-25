<?php

class actionSitemapRobots extends cmsAction {

    public function run() {

        header('Content-Disposition: inline; filename="robots.txt"');
        header('Content-type: text/plain');

        echo str_replace("\r\n", "\n", trim($this->options['robots'])) . "\n";

        echo "Sitemap: " . href_to_home(true) . "sitemap.xml\n";

        exit;
    }

}
