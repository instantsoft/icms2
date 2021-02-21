<?php

class onContentWysiwygLinksList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($ctype_name, $target_id) {

        $urls = [];

        if (empty($ctype_name)) {
            return $urls;
        }

        $is_ctype_exists = $this->model->getContentTypeByName($ctype_name);
        if (!$is_ctype_exists) {
            return $urls;
        }

        $items = $this->model->limit(500)->getContentItemsForSitemap($ctype_name, ['title']);

        if ($items) {

            $urls[] = ['url' => '', 'name' => ''];

            foreach ($items as $item) {
                $urls[] = [
                    'url'  => href_to($ctype_name, $item['slug'] . '.html'),
                    'name' => htmlspecialchars($item['title'])
                ];
            }
        }

        return $urls;
    }

}
