<?php

class actionRssFeed extends cmsAction {

    private $cache_file_path;

    public $request_params = [
        'template' => [
            'default' => '',
            'rules'   => [
                ['sysname']
            ]
        ]
    ];

    public function run($ctype_name) {

        if ($this->validate_sysname($ctype_name) !== true) {
            return cmsCore::error404();
        }

        $feed = $this->model->getFeedByCtypeName($ctype_name);
        if (!$feed || !$feed['is_enabled']) {
            return cmsCore::error404();
        }

        if ($feed['is_cache']) {

            $this->cache_file_path = $this->cms_config->cache_path . 'rss/' . md5($ctype_name . serialize($this->request->getData())) . '.rss';

            if ($this->isDisplayCached($feed)) {
                return $this->displayCached();
            }
        }

        if ($this->model->isCtypeFeed($ctype_name)) {
            $ctype_name = 'content';
        }

        if (!cmsCore::isControllerExists($ctype_name)) {
            return cmsCore::error404();
        }

        $controller = cmsCore::getController($ctype_name, $this->request);

        if (!$controller->isEnabled()) {
            return cmsCore::error404();
        }

        $data = $controller->runHook('rss_feed_list', [$feed]);

        if (!$data || $data === $this->request->getData()) {
            return cmsCore::error404();
        }

        list($feed, $category, $author) = $data;

        // Преобразовываем относительные ссылки в абсолютные
        if (!empty($feed['items'])) {
            foreach ($feed['items'] as $key => $item) {
                if (!empty($feed['mapping']['description']) && !empty($item[$feed['mapping']['description']])) {
                    $feed['items'][$key][$feed['mapping']['description']] = preg_replace(
                        ['#"\/upload\/#u', '#"\/static\/#u'],
                        ['"'.$this->cms_config->upload_host_abs.'/', '"'.$this->cms_config->host.'/static/'],
                        $item[$feed['mapping']['description']]
                    );
                }
            }
        }

        header('Content-type: application/rss+xml; charset=utf-8');

        $template = $this->request->get('template');

        if ($template) {
            if ($this->cms_template->getTemplateFileName('controllers/' . $this->name . '/' . $template, true)) {
                $feed['template'] = $template;
            } else {
                return cmsCore::error404();
            }
        }

        if ($category){ $feed['title'] .=' / '.$category['title']; }
        if ($author){ $feed['title'] = $author['nickname'].' — '.$feed['title']; }

        $rss = $this->cms_template->getRenderedChild($feed['template'], [
            'feed_title' => sprintf(LANG_RSS_FEED_TITLE_FORMAT, $feed['title'], $this->cms_config->sitename),
            'feed'       => $feed,
            'category'   => $category,
            'author'     => $author
        ]);

        if ($feed['is_cache']) {
            $this->cacheRss($rss);
        }

        return $this->halt($rss);
    }

    private function displayCached() {

        header('Content-type: application/rss+xml; charset=utf-8');

        echo file_get_contents($this->cache_file_path);

        die;
    }

    private function isDisplayCached($feed) {

        if (file_exists($this->cache_file_path)) {

            // проверяем время жизни
            if ((filemtime($this->cache_file_path) + ($feed['cache_interval'] * 60)) > time()) {

                return true;
            } else {

                @unlink($this->cache_file_path);
            }
        }

        return false;
    }

    private function cacheRss($feed) {

        if (!is_writable(dirname($this->cache_file_path))) {
            return false;
        }

        file_put_contents($this->cache_file_path, $feed);

        return;
    }

}
