<?php

class actionSitemapRobots extends cmsAction {

    public function run() {

        $body  = str_replace("\r\n", "\n", trim($this->options['robots'])) . "\n";
        $body .= "Sitemap: " . href_to_home(true) . "sitemap.xml\n";

        $this->cms_core->response->
                setHeader('Content-Type', 'text/plain;charset=UTF-8')->
                setHeader('Content-Disposition', 'inline; filename="robots.txt"')->
                setContent($body)->sendAndExit();
    }

}
