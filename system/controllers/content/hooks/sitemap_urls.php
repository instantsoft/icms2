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

        if ($action === 'items') {

            if (cmsPermissions::getRuleSubjectPermissions('content', $ctype['name'], 'view_list')) {
                return $urls;
            }

            list($ctype, $this->model) = cmsEventsManager::hook('content_list_sitemap_filter', [$ctype, $this->model]);
            list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_sitemap_filter", [$ctype, $this->model]);

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

        if ($action === 'cats') {

            // Главная
            if (empty($ctype['options']['list_off_index'])) {
                $urls[] = [
                    'last_modified' => date('Y-m-d'),
                    'title'         => $ctype['title'],
                    'url'           => href_to_abs($ctype['name'])
                ];
            }

            // Получаем список наборов
            $datasets = $this->getCtypeDatasets($ctype, [
                'cat_id' => false
            ]);

            if ($datasets && count($datasets) > 1) {

                $ds_counter = 0;

                foreach($datasets as $ds){

                    // На первый набор не надо
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

            $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

            if ($ctype['is_cats']) {

                list($ctype, $this->model) = cmsEventsManager::hook('content_list_sitemap_cats_filter', [$ctype, $this->model]);
                list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_sitemap_cats_filter", [$ctype, $this->model]);

                $cats = $this->model->limit(false)->getCategoriesTree($ctype['name'], false);

                if ($cats) {
                    foreach ($cats as $item) {
                        $urls[] = [
                            'last_modified' => null,
                            'title'         => $item['title'],
                            'url'           => href_to_abs($base_url, $item['slug'])
                        ];
                    }
                }
            } else {
                $cats = [];
            }

            // Фильтры
            if($this->model->isFiltersTableExists($ctype['name'])) {

                $filters = $this->model->selectOnly('slug')->
                        select('title')->select('cats')->select('id')->
                        limit(false)->getContentFilters($ctype['name']);

                if($filters){

                    // Корневые фильтры
                    foreach ($filters as $filter) {

                        // заданы категории, но нет в них корневой
                        if($filter['cats'] && !in_array(1, $filter['cats'])){
                            continue;
                        }

                        $urls[] = [
                            'last_modified' => null,
                            'title'         => $filter['title'],
                            'url'           => href_to_abs($base_url, $filter['slug'])
                        ];
                    }

                    // Фильтры для категорий
                    if ($cats) {
                        foreach ($cats as $cat) {

                            $cat_url = href_to_abs($base_url, $cat['slug']);

                            foreach ($filters as $filter) {

                                if($filter['cats'] && !in_array($cat['id'], $filter['cats'])){
                                    continue;
                                }

                                $urls[] = [
                                    'last_modified' => null,
                                    'title'         => $cat['title'].' / '.$filter['title'],
                                    'url'           => $cat_url.'/'.$filter['slug']
                                ];
                            }
                        }
                    }

                }
            }

        }

        return $urls;
    }

}
