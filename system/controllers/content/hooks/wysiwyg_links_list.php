<?php

class onContentWysiwygLinksList extends cmsAction {

    public function run($ctype_name, $target_id){

        $urls = array();

        if (empty($ctype_name)) { return $urls; }

		$is_ctype_exists = $this->model->getContentTypeByName($ctype_name);
		if (!$is_ctype_exists) { return $urls; }

        $items = $this->model->limit(500)->getContentItemsForSitemap($ctype_name, array('title'));

        if ($items){
            $urls[] = array('url' => '', 'name' => '');
            foreach($items as $item){
                $urls[] = array(
                    'url'  => href_to($ctype_name, $item['slug'].'.html'),
                    'name' => htmlspecialchars($item['title'])
                );
            }
        }

        return $urls;

    }

}
