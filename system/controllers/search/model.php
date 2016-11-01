<?php

class modelSearch extends cmsModel{

    protected $query;
    protected $original_query;
    protected $type;
    protected $date_interval;

    public function setQuery($query){

        $this->original_query = $query;

        $this->query = array();

        $stopwords = string_get_stopwords(cmsCore::getLanguageName());

        $words = explode(' ', $query);

        foreach($words as $word){

            $word = strip_tags(mb_strtolower(trim(urldecode($word))));

            if (mb_strlen($word)<3 || is_numeric($word)) { continue; }
            if($stopwords && in_array($word, $stopwords)){ continue; }
            if (mb_strlen($word)==3) { $this->query[] = $this->db->escape($word); continue; }

            if (mb_strlen($word) >= 12) {
                $word = mb_substr($word, 0, mb_strlen($word) - 4);
            } else if (mb_strlen($word) >= 10) {
                $word = mb_substr($word, 0, mb_strlen($word) - 3);
            } else if (mb_strlen($word) >= 6) {
                $word = mb_substr($word, 0, mb_strlen($word) - 2);
            } else {
                $word = mb_substr($word, 0, mb_strlen($word) - 1);
            }

            $this->query[] = $this->db->escape($word) . '*';

        }

        if (empty($this->query)) { return false; }

        return true;

    }

    public function setSearchType($type){
        $this->type = $type;
    }

    public function setDateInterval($date){
        $this->date_interval = $date;
    }

    public function getFullTextQuery(){

        $ft_query = '';

        switch ($this->type){

            case 'words':
                $ft_query .= '>\"' . $this->db->escape($this->original_query).'\" <';
                $ft_query .= '+' . implode(' +', $this->query);
                break;

            case 'exact':
                $ft_query .= '\"' . $this->db->escape($this->original_query) . '\"';
                break;

        }

        return $ft_query;

    }

    public function getSearchSQL($table_name, $match_fields, $select_fields, $filters){

        $match_fields  = implode(', ', $match_fields);
        $select_fields = implode(', ', $select_fields);

        $query = $this->getFullTextQuery();

        $filter_sql = '';
        if($filters){
            foreach ($filters as $filter) {
                $filter['value'] = $this->db->prepareValue($filter['field'], $filter['value']);
                $filter_sql[] = "`{$filter['field']}` {$filter['condition']} {$filter['value']}";
            }
            $filter_sql = implode(' AND ', $filter_sql).' AND ';
        }

        $sql = "SELECT {$select_fields}
                FROM {#}{$table_name}
                WHERE {$filter_sql} MATCH({$match_fields}) AGAINST ('{$query}' IN BOOLEAN MODE)
                ";

        if ($this->date_interval != 'all'){

            switch ($this->date_interval){
                case 'w':
                    $sql .= "AND DATEDIFF(NOW(), date_pub) <= 7 \n";
                    break;
                case 'm':
                    $sql .= "AND DATE_SUB(NOW(), INTERVAL 1 MONTH) < date_pub \n";
                    break;
                case 'y':
                    $sql .= "AND DATE_SUB(NOW(), INTERVAL 1 YEAR) < date_pub \n";
                    break;
            }

        }

        return $sql;

    }

    public function getSearchResultsCount($table_name, $match_fields, $filters){

        $sql = $this->getSearchSQL($table_name, $match_fields, array('1'), $filters);

        $sql_result = $this->db->query($sql);

        return $this->db->numRows($sql_result);

    }

    public function getSearchResults($table_name, $match_fields, $select_fields, $filters, $item_callback=false, $sources_name = ''){

        $sql = $this->getSearchSQL($table_name, $match_fields, $select_fields, $filters);

        if ($this->limit){ $sql .= " LIMIT {$this->limit}"; }

        $sql_result = $this->db->query($sql);

        if (!$this->db->numRows($sql_result)){
            return false;
        }

        $items = array();

        while ($item = $this->db->fetchAssoc($sql_result)){

            foreach($match_fields as $field_name){
                $item[$field_name] = $this->getHighlightedText($item[$field_name]);
            }

            if (is_callable($item_callback)){
                $item = call_user_func_array($item_callback, array($item, $this, $sources_name, $match_fields, $select_fields));
                if ($item === false){ continue; }
            }

            $items[] = $item;

        }

        return $items;

    }

    public function getHighlightedText($text){

        $text = str_replace(array("\n", '<br>', '<br/>'), ' ', $text);
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text);

        $found_words = array();
        $found_sentences = array();

        foreach ($this->query as $word){

            $word = preg_quote(rtrim($word, '*'));

            if (preg_match("/\b({$word}\w+)\b/iu", $text, $matches)){
                $found_words[] = $matches[0];
            }

        }

        $sentences = explode('.', $text);
        $sentences = array_map(function($s){ return trim($s); }, $sentences);

        foreach($sentences as $sentence){
            $is_found = false;
            foreach($found_words as $word){
                if (mb_strpos(mb_strtolower($sentence), mb_strtolower($word)) !== false){
                    $sentence = str_replace($word, '<em>'.$word.'</em>', $sentence);
                    $is_found = true;
                }
            }
            if ($is_found) { $found_sentences[] = $sentence; }
        }

        if (!$found_sentences) { return $text; }

        $found_sentences = implode('... ', $found_sentences);

        return $found_sentences.'...';

    }

}
