<?php

class onRssContentBeforeProfile extends cmsAction {

    public function run($data){

        list($ctype, $profile) = $data;

        if (!empty($ctype['options']['is_rss'])){

            $title = $profile['nickname'] . ' - ' . $ctype['title'];

            $feed_title = sprintf(LANG_RSS_FEED_TITLE_FORMAT, $title, cmsConfig::get('sitename'));
            $feed_url = href_to_abs($this->name, 'feed', $ctype['name']) . '?user=' . $profile['id'];

            $link_tag = '<link title="'.html($feed_title, false).'" type="application/rss+xml" rel="alternate" href="'.$feed_url.'">';

            cmsTemplate::getInstance()->addHead($link_tag);

        }

        return array($ctype, $profile);

    }

}
