<?php

class onSitemapEngineStart extends cmsAction {

    public function run(){

        if($this->cms_core->uri_controller === 'robots.txt'){
            $this->cms_core->uri_controller = 'sitemap';
            $this->cms_core->uri_action = 'robots';
        }

        return true;

    }

}
