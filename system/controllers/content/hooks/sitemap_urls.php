<?php

class onContentSitemapUrls extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($ctype_name) {

        $urls   = [];
        $action = 'items';

        if (empty($ctype_name)) {
            return $urls;
        }

        if (is_array($ctype_name)) {
            list($ctype_name, $action) = $ctype_name;
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { return $urls; }

        if ($action == 'items') {

            if (cmsPermissions::getRuleSubjectPermissions('content', $ctype['name'], 'view_list')) {
                return $urls;
            }

            list($ctype, $this->model) = cmsEventsManager::hook('content_list_sitemap_filter', array($ctype, $this->model));
            list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_sitemap_filter", array($ctype, $this->model));

            $items = $this->model->limit(false)->getContentItemsForSitemap($ctype['name']);

            if ($items) {
                foreach ($items as $item) {
                    $urls[] = [
                        'last_modified' => $item['date_last_modified'],
                        'title'         => $item['title'],
                        'url'           => href_to_abs($ctype['name'], $item['slug'] . '.html')
                    ];
                }
            }
        }

        if ($action == 'cats') {

            // Главная
            if (empty($ctype['options']['list_off_index'])) {
                $urls[] = [
                    'last_modified' => date('Y-m-d'),
                    'title'         => $ctype['title'],
                    'url'           => href_to_abs($ctype['name'])
                ];
            }

            // Получаем список наборов
            $datasets = $this->getCtypeDatasets($ctype, array(
                'cat_id' => false
            ));

            if ($datasets && count($datasets) > 1) {

                $ds_counter = 0;

                foreach($datasets as $ds){

                    if($ds_counter){
                        $urls[] = [
                            'last_modified' => date('Y-m-d'),
                            'title'         => $ds['title'],
                            'url'           => href_to_abs($ctype['name']).'-'.$ds['name']
                        ];
                    }

                    $ds_counter++;
                }
            }

            if ($ctype['is_cats']) {

                list($ctype, $this->model) = cmsEventsManager::hook('content_list_sitemap_cats_filter', array($ctype, $this->model));
                list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_sitemap_cats_filter", array($ctype, $this->model));

                $items = $this->model->limit(false)->getCategoriesTree($ctype['name'], false);

                $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

                if ($items) {
                    foreach ($items as $item) {
                        $urls[] = [
                            'last_modified' => null,
                            'title'         => $item['title'],
                            'url'           => href_to_abs($base_url, $item['slug'])
                        ];
                    }
                }
            }

        }

        return $urls;
    }

}
