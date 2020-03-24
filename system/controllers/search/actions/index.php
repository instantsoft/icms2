<?php

class actionSearchIndex extends cmsAction {

    public function run($target = false){

        $query = $this->request->get('q', '');
        $type  = $this->request->get('type', 'words');
        $date  = $this->request->get('date', 'all');
        $page  = $this->request->get('page', 1);

        if (!in_array($type, array('words', 'exact'), true)){ cmsCore::error404(); }
        if (!in_array($date, array('all', 'w', 'm', 'y'), true)){ cmsCore::error404(); }
        if (!is_numeric($page)){ cmsCore::error404(); }

        if($target && $this->validate_sysname($target) !== true){
            cmsCore::error404();
        }

        if ($this->request->has('q')){

            if (!$query || !$this->model->setQuery($query)) {

                cmsUser::addSessionMessage(LANG_SEARCH_TOO_SHORT, 'error');

                $this->redirectToAction('');

            }

            $this->model->setSearchType($type);
            $this->model->setDateInterval($date);
            $this->model->limitPage($page, $this->options['perpage']);

            $search_controllers = cmsEventsManager::hookAll('fulltext_search', false, array());

            if (!$target){

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
                    if(empty($search_controller['match_fields'][$sources_name])){
                        continue;
                    }

                    // есть ли что-то по поисковому запросу у этого назначения?
                    $results_count = $this->model->getSearchResultsCount(
                            $search_controller['table_names'][$sources_name],
                            $search_controller['match_fields'][$sources_name],
                            $search_controller['filters'][$sources_name]
                    );

                    // сами результаты ищем только у первого найденного
                    // или у переданного
                    // для остальных считаем количество
                    if ($results_count){

                        $result = array();

                        if(!$is_results_found && (($target && $target == $sources_name) || !$target)){

                            $result = $this->model->getSearchResults(
                                    $search_controller['table_names'][$sources_name],
                                    $search_controller['match_fields'][$sources_name],
                                    $search_controller['select_fields'][$sources_name],
                                    $search_controller['filters'][$sources_name],
                                    $search_controller['item_callback'],
                                    $sources_name
                            );

                            $result = cmsEventsManager::hook("content_{$sources_name}_search_list", $result);

                            $is_results_found = true;

                            $target = $sources_name;
                            $target_title = $sources_title;

                        }

                        $results[] = array(
                            'title' => $sources_title,
                            'name'  => $sources_name,
                            'items' => $result,
                            'count' => $results_count
                        );

                    }

                }
            }

        }

        // если есть отдельный шаблон, используем его
        $tpl = 'index_'.$target;
        if(!$this->cms_template->getTemplateFileName('controllers/search/'.$tpl, true)){
            $tpl = 'index';
        }

        $this->cms_template->addHead('<link rel="canonical" href="'.(!$target ? href_to_abs($this->name) : href_to_abs($this->name, $target)).'?q='.urlencode($query).'"/>');

        return $this->cms_template->render($tpl, array(
            'user'         => $this->cms_user,
            'query'        => $query,
            'type'         => $type,
            'date'         => $date,
            'target'       => $target,
            'target_title' => (!empty($target_title) ? mb_strtolower($target_title) : ''),
            'page'         => $page,
            'perpage'      => $this->options['perpage'],
            'results'      => (isset($results) ? $results : false),
            'page_url'     => (isset($page_url) ? $page_url : false)
        ));

    }

}
