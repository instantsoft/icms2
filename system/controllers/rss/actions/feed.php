<?php

class actionRssFeed extends cmsAction {

    private $cache_file_path;

    public $request_params = array(
        'template' => array(
            'default' => '',
            'rules'   => array(
                array('sysname')
            )
        )
    );

    public function run($ctype_name=false){

        if (!$ctype_name || $this->validate_sysname($ctype_name) !== true) { cmsCore::error404(); }

        $feed = $this->model->getFeedByCtypeName($ctype_name);
        if (!$feed || !$feed['is_enabled']) {
            cmsCore::error404();
        }

        if ($feed['is_cache']) {

            $this->cache_file_path = cmsConfig::get('cache_path').'rss/'.md5($ctype_name.serialize($this->request->getData())).'.rss';

            if($this->isDisplayCached($feed)){
                return $this->displayCached();
            }

        }

        if($this->model->isCtypeFeed($ctype_name)){
            $ctype_name = 'content';
        }

        if(!cmsCore::isControllerExists($ctype_name)){
            cmsCore::error404();
        }

        $controller = cmsCore::getController($ctype_name, $this->request);

        if(!$controller->isEnabled()){
            cmsCore::error404();
        }

        $data = $controller->runHook('rss_feed_list', array($feed));

        if(!$data || $data === $this->request->getData()){
            cmsCore::error404();
        }

        list($feed, $category, $author) = $data;

		header('Content-type: application/rss+xml; charset=utf-8');

        $template = $this->request->get('template');

        if($template){
            if($this->cms_template->getTemplateFileName('controllers/'.$this->name.'/'.$template, true)){
                $feed['template'] = $template;
            } else {
                cmsCore::error404();
            }
        }

        $rss = $this->cms_template->getRenderedChild($feed['template'], array(
            'feed'     => $feed,
            'category' => $category,
            'author'   => $author
        ));

        if ($feed['is_cache']) {
            $this->cacheRss($rss);
        }

        $this->halt($rss);

    }

    private function displayCached() {

        header('Content-type: application/rss+xml; charset=utf-8');

        echo file_get_contents($this->cache_file_path);

        die;

    }

    private function isDisplayCached($feed) {

        if(file_exists($this->cache_file_path)){

            // проверяем время жизни
            if((filemtime($this->cache_file_path) + ($feed['cache_interval']*60)) > time()){

                return true;

            } else {

                @unlink($this->cache_file_path);

            }

        }

        return false;

    }

    private function cacheRss($feed) {

        if (!is_writable(dirname($this->cache_file_path))){ return false; }

        file_put_contents($this->cache_file_path, $feed);

    }

}
