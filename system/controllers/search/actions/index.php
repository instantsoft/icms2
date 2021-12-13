<?php
/**
 * @property \modelSearch $model
 */
class actionSearchIndex extends cmsAction {

    public function run($target = false) {

        $default_order_by = !empty($this->options['order_by']) ? $this->options['order_by']: 'fsort';

        $query    = $this->request->get('q', '');
        $type     = $this->request->get('type', 'words');
        $date     = $this->request->get('date', 'all');
        $order_by = $this->request->get('order_by', $default_order_by);
        $page     = $this->request->get('page', 1);

        if (!in_array($order_by, ['fsort', 'date_pub'], true)) {
            return cmsCore::error404();
        }
        if (!in_array($type, ['words', 'exact'], true)) {
            return cmsCore::error404();
        }
        if (!in_array($date, ['all', 'w', 'm', 'y'], true)) {
            return cmsCore::error404();
        }
        if (!is_numeric($page)) {
            return cmsCore::error404();
        }

        if ($target && $this->validate_sysname($target) !== true) {
            return cmsCore::error404();
        }

        if ($this->request->has('q')) {

            if (!$query || !$this->model->setQuery($query)) {

                cmsUser::addSessionMessage(LANG_SEARCH_TOO_SHORT, 'error');

                return $this->redirectToAction('');
            }

            $this->model->setSearchType($type);

            $search_controllers = cmsEventsManager::hookAll('fulltext_search', $this, []);

            if (!$target) {
                $page_url = href_to($this->name);
            } else {
                $page_url = href_to($this->name, $target);
            }

            // найден ли результат
            $is_results_found = false;

            foreach ($search_controllers as $search_controller) {

                $search_controller = cmsEventsManager::hook("search_{$search_controller['name']}_data", $search_controller);

                foreach ($search_controller['sources'] as $sources_name => $sources_title) {

                    // выключено?
                    if (!empty($this->options['types']) &&
                            !in_array($sources_name, $this->options['types'])) {
                        continue;
                    }

                    // есть поля для поиска?
                    if (empty($search_controller['match_fields'][$sources_name])) {
                        continue;
                    }

                    // Фильтр по дате
                    $this->model->filterDateInterval($date);

                    // Дополнительные таблицы
                    if(!empty($search_controller['joins'][$sources_name])){
                        $this->model->applyJoins($search_controller['joins'][$sources_name]);
                    }

                    // Дополнительная фильтрация
                    if(!empty($search_controller['filters'][$sources_name])){
                        $this->model->filterSearch($search_controller['filters'][$sources_name]);
                    }

                    // Поля, по которым ищем
                    $this->model->setMatchFields($search_controller['match_fields'][$sources_name])->filterQuery();

                    // есть ли что-то по поисковому запросу у этого назначения?
                    $results_count = $this->model->getCount($search_controller['table_names'][$sources_name], false);

                    // сами результаты ищем только у первого найденного
                    // или у переданного
                    // для остальных считаем количество
                    if ($results_count) {

                        $result = [];

                        if (!$is_results_found && (($target && $target == $sources_name) || !$target)) {

                            // Поля, какие хотим подсветить, если они отличаются от match_fields
                            if(!empty($search_controller['highlight_fields'][$sources_name])){
                                $this->model->setHighlightFields($search_controller['highlight_fields'][$sources_name]);
                            }

                            // Если по трём символам, то сортировка принудительно по дате
                            if($this->model->isThreeSymbolSearch()){
                                $order_by = 'date_pub';
                            }
                            // Сортировка
                            $this->model->orderByRaw(($order_by === 'date_pub' ? 'i.' : '').$order_by.' desc');

                            // Разбивка на страницы
                            $this->model->limitPage($page, $this->options['perpage']);

                            // Поля для выборки
                            $this->model->selectList($search_controller['select_fields'][$sources_name]);

                            $result = $this->model->getSearchResults($search_controller['table_names'][$sources_name]);

                            // Применяем коллбэк
                            foreach ($result as $key => $item) {

                                if (is_callable($search_controller['item_callback'])) {

                                    $result[$key] = call_user_func_array($search_controller['item_callback'], [
                                        $item,
                                        $this->model,
                                        $sources_name,
                                        $search_controller['match_fields'][$sources_name],
                                        $search_controller['select_fields'][$sources_name]
                                    ]);

                                    if ($result[$key] === false) {
                                        unset($result[$key]);
                                    }
                                }
                            }

                            $result = cmsEventsManager::hook("content_{$sources_name}_search_list", $result);

                            $is_results_found = true;

                            $target       = $sources_name;
                            $target_title = $sources_title;
                        }

                        $results[] = [
                            'title' => $sources_title,
                            'name'  => $sources_name,
                            'items' => $result,
                            'count' => $results_count
                        ];
                    }

                    $this->model->resetFilters();
                }
            }
        }

        // если есть отдельный шаблон, используем его
        $tpl = 'index_' . $target;
        if (!$this->cms_template->getTemplateFileName('controllers/search/' . $tpl, true)) {
            $tpl = 'index';
        }

        $this->cms_template->addHead('<link rel="canonical" href="' . (!$target ? href_to_abs($this->name) : href_to_abs($this->name, $target)) . '?q=' . urlencode($query) . '"/>');

        return $this->cms_template->render($tpl, [
            'user'         => $this->cms_user,
            'order_by'     => $order_by,
            'query'        => $query,
            'type'         => $type,
            'date'         => $date,
            'target'       => $target,
            'target_title' => (!empty($target_title) ? mb_strtolower($target_title) : ''),
            'page'         => $page,
            'perpage'      => $this->options['perpage'],
            'results'      => (isset($results) ? $results : false),
            'page_url'     => (isset($page_url) ? $page_url : false)
        ]);
    }

}
