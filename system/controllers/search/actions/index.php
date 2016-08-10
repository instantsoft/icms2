<?php

class actionSearchIndex extends cmsAction {

    private $default_sql_fields;

    public function __construct($controller, $params=array()) {
        parent::__construct($controller, $params);
        $this->default_sql_fields = $this->model->getDefaultSqlFields();
    }

    public function run($ctype_name=false){

        $query = $this->request->get('q', false);
        $type  = $this->request->get('type', 'words');
        $date  = $this->request->get('date', 'all');
        $page  = $this->request->get('page', 1);

        if (!in_array($type, array('words', 'exact'), true)){ cmsCore::error404(); }
        if (!in_array($date, array('all', 'w', 'm', 'y'), true)){ cmsCore::error404(); }
        if (!is_numeric($page)){ cmsCore::error404(); }

        if ($this->request->has('q')){
            if (!$query) { $this->redirectToAction(''); }
            $results = $this->search($query, $type, $date, $ctype_name, $page);
            if ($results && !$ctype_name){
                $ctype_name = $results[0]['name'];
                $page_url = href_to($this->name);
            } else {
                $page_url = href_to($this->name, 'index', $ctype_name);
            }
        }

        return cmsTemplate::getInstance()->render('index', array(
            'query'      => $query,
            'type'       => $type,
            'date'       => $date,
            'ctype_name' => $ctype_name,
            'page'       => $page,
            'perpage'    => $this->options['perpage'],
            'results'    => isset($results) ? $results : false,
            'page_url'   => isset($page_url) ? $page_url : false
        ));

    }

    public function search($query, $type, $date, $ctype_name, $page=1){

        $user = cmsUser::getInstance();

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        $results = array();

        if (!$this->model->setQuery($query)){
            cmsUser::addSessionMessage(LANG_SEARCH_TOO_SHORT, 'error');
            return false;
        }

        $this->model->setSearchType($type);
        $this->model->setDateInterval($date);
        $this->model->limitPage($page, $this->options['perpage']);

        $is_results_found = false;

        $allowed_ctypes = $this->options['ctypes'];

        foreach($ctypes as $ctype){

            if (!in_array($ctype['name'], $allowed_ctypes)) { continue; }

            $result         = array();
            $sql_fields     = array();
            $default_fields = $this->default_sql_fields;

            $fields = $content_model->getContentFields($ctype['name']);

            foreach($fields as $field){

                // индексы создаются только на поля типа caption, text, html
                // в настройках полей должно быть включено их участие в индексе
                $is_text = in_array($field['type'], array('caption', 'text', 'html')) && $field['handler']->getOption('in_fulltext_search');

                if ($is_text && !$field['is_private'] && (!$field['groups_read'] || $user->isInGroups($field['groups_read']))){
                    $sql_fields[] = $field['name'];
                }
                if ($field['name'] == 'photo' && !$field['is_private'] && (!$field['groups_read'] || $user->isInGroups($field['groups_read']))){
                    $default_fields[] = $field['name'];
                }
            }

            // если нет полей для поиска, пропускаем
            if(!$sql_fields){
                continue;
            }

            $table_name = $content_model->getContentTypeTableName($ctype['name']);

            $results_count = $this->model->getSearchResultsCount($table_name, $sql_fields);

            if ($results_count){

                if ($ctype_name == $ctype['name'] || (!$ctype_name && !$is_results_found)){

                    $result = $this->model->getSearchResults($table_name, $sql_fields, $default_fields, function($item, $model) use ($ctype) {

                        if(!empty($item['photo'])){
                            $item['photo'] = html_image($item['photo'], 'small', $item['title']);
                            if(!$item['photo']){ unset($item['photo']); }
                        }

                        $item['url'] = href_to($ctype['name'], $item['slug'].'.html');

                        return $item;

                    });

                    $result = cmsEventsManager::hook("content_{$ctype['name']}_search_list", $result);

                    $is_results_found = true;

                }

                $results[] = array(
                    'title' => $ctype['title'],
                    'name' => $ctype['name'],
                    'items' => $result,
                    'count' => $results_count
                );

            }

        }

        // результаты от других контроллеров
        $components_results = cmsEventsManager::hookAll('fulltext_search', array($this->model, $ctype_name));
        if($components_results){
            foreach ($components_results as $components_result) {
                $results[] = $components_result;
            }
        }

        return $results;

    }

}

