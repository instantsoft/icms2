<?php

class onSitemapEngineStart extends cmsAction {

    public function run() {

        if (in_array('robots.txt', [$this->cms_core->uri_action, $this->cms_core->uri_controller], true)) {
            $this->cms_core->uri_controller = 'sitemap';
            $this->cms_core->uri_action     = 'robots';
        }

        return true;
    }

}
