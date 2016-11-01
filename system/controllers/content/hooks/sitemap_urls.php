<?php

class onContentSitemapUrls extends cmsAction {

    public function run($ctype_name){

        $urls = array();

        if (empty($ctype_name)) { return $urls; }

		$is_ctype_exists = $this->model->getContentTypeByName($ctype_name);
		if (!$is_ctype_exists) { return $urls; }

        $items = $this->model->limit(false)->getContentItemsForSitemap($ctype_name);

        if ($items){
            foreach($items as $item){
                $url = href_to_abs($ctype_name, $item['slug'].'.html');
                $date_last_modified = $item['date_last_modified'];
                $urls[$url] = $date_last_modified;
            }
        }

        return $urls;

    }

}
