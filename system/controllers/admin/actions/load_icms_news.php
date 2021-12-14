<?php

class actionAdminLoadIcmsNews extends cmsAction {

    private $news_link = [
        'icms'       => 'https://api.instantcms.ru/rss/feed/pages?category=2',
        'icms_blogs' => 'https://api.instantcms.ru/rss/feed/blogs',
        'icms_docs'  => 'https://docs.instantcms.ru/feed.php'
    ];

    private $news_count = 10;

    public function run($target) {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        if (!$target || !in_array($target, array_keys($this->news_link))) {
            cmsCore::error404();
        }

        cmsCore::loadLib('lastrss.class');

        $rss = new lastRSS;

        $rss->cache_dir   = cmsConfig::get('cache_path');
        $rss->cache_time  = 3600;
        $rss->stripHTML   = true;
        $rss->cp          = 'UTF-8';
        $rss->items_limit = $this->news_count;

        $items = [];

        $res = $rss->get($this->news_link[$target]);

        if (!empty($res['items'])) {
            foreach ($res['items'] as $item) {

                $item['target_title'] = empty($res['title']) ?
                        (empty($res['image_title']) ? '' : $res['image_title']) :
                        $res['title'];

                $item['target_description'] = empty($res['description']) ?
                        (empty($res['image_description']) ? '' : $res['image_description']) :
                        $res['description'];

                $items[] = $item;
            }
        }

        if (!$items) {
            $this->halt(LANG_NO_ITEMS);
        }

        $this->cms_template->renderPlain('index_news_data', [
            'items' => $items
        ]);
    }

}
