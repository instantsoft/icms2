<?php

class onRssContentBeforeCategory extends cmsAction {

    public function run($data){

        list($ctype, $category) = $data;

        if (!empty($ctype['options']['is_rss'])){

            $title = $ctype['title'];

            if ($category['id'] > 1){ $title .= ' / ' . $category['title']; }

            $feed_title = sprintf(LANG_RSS_FEED_TITLE_FORMAT, $title, cmsConfig::get('sitename'));
            $feed_url = href_to_abs($this->name, 'feed', $ctype['name']);

            if ($category['id'] > 1){ $feed_url .= '?category=' . $category['id']; }

            $link_tag = '<link title="'.html($feed_title, false).'" type="application/rss+xml" rel="alternate" href="'.$feed_url.'">';

            cmsTemplate::getInstance()->addHead($link_tag);

        }

        return array($ctype, $category);

    }

}
