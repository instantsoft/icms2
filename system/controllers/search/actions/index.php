<?php

class actionSearchIndex extends cmsAction {

    public function run($ctype_name=false){

        $query = $this->request->get('q', false);
        $type = $this->request->get('type', 'words');
        $date = $this->request->get('date', 'all');
        $page = $this->request->get('page', 1);

        if (!in_array($type, array('words', 'exact'))){ cmsCore::error404(); }
        if (!in_array($date, array('all', 'w', 'm', 'y'))){ cmsCore::error404(); }
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
            'query' => $query,
            'type' => $type,
            'date' => $date,
            'ctype_name' => $ctype_name,
            'page' => $page,
            'perpage' => $this->options['perpage'],
            'results' => isset($results) ? $results : false,
            'page_url' => isset($page_url) ? $page_url : false
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

        $is_results_found = false;

        $allowed_ctypes = $this->options['ctypes'];
        $perpage = $this->options['perpage'];

        foreach($ctypes as $ctype){

            if (!in_array($ctype['name'], $allowed_ctypes)) { continue; }

            $result = array();

            $fields = $content_model->getContentFields($ctype['name']);
            $sql_fields = array();

            foreach($fields as $field){
                $is_text = in_array($field['type'], array('caption', 'text', 'html'));
                if ($is_text && !$field['is_private'] && (!$field['groups_read'] || $user->isInGroups($field['groups_read']))){
                    $sql_fields[] = $field['name'];
                }
            }

            $table_name = $content_model->getContentTypeTableName($ctype['name']);

            $results_count = $this->model->getSearchResultsCount($table_name, $sql_fields);

            if ($results_count){

                if ($ctype_name == $ctype['name'] || (!$ctype_name && !$is_results_found)){
                    $result = $this->model->getSearchResults($table_name, $sql_fields, $page, $perpage);
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

        return $results;

    }

}

