<?php

class modelSearch extends cmsModel{

    protected $query;
    protected $original_query;
    protected $type;
    protected $date_interval;

    public function setQuery($query){

        $this->original_query = $query;

        $this->query = array();

        $words = explode(' ', $query);

        foreach($words as $word){

            $word = strip_tags(mb_strtolower(trim(urldecode($word))));

            if (mb_strlen($word)<3) { continue; }
            if (mb_strlen($word)==3) { $this->query[] = $this->db->escape($word) . '*'; continue; }

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

    public function getSearchSQL($table_name, $fields){

        $query = $this->getFullTextQuery();

        $sql_fields = implode(', ', $fields);

        $sql = "SELECT id, slug, date_pub, {$sql_fields}
                FROM {#}{$table_name}
                WHERE is_pub = 1 AND MATCH({$sql_fields}) AGAINST ('{$query}' IN BOOLEAN MODE)
                ";

        if ($this->date_interval != 'all'){

            switch ($this->date_interval){
                case 'w':
                    $sql .= "AND DATEDIFF(NOW(), date_pub) <= 7";
                    break;
                case 'm':
                    $sql .= "AND DATE_SUB(NOW(), INTERVAL 1 MONTH) < date_pub";
                    break;
                case 'y':
                    $sql .= "AND DATE_SUB(NOW(), INTERVAL 1 YEAR) < date_pub";
                    break;
            }

        }

        return $sql;

    }

    public function getSearchResultsCount($table_name, $fields){

        $sql = $this->getSearchSQL($table_name, $fields);

        $sql_result = $this->db->query($sql);

        return $this->db->numRows($sql_result);

    }

    public function getSearchResults($table_name, $fields, $page=1, $perpage=15){

        $sql = $this->getSearchSQL($table_name, $fields);

        $limit_start = ($page-1)*$perpage;

        $sql .= " LIMIT {$limit_start}, {$perpage}";

        $sql_result = $this->db->query($sql);

        if (!$this->db->numRows($sql_result)){
            return false;
        }

        $items = array();

        while ($item = $this->db->fetchAssoc($sql_result)){

            foreach($fields as $field_name){
                if ($field_name != 'title'){
                    $item[$field_name] = $this->getHighlightedText($item[$field_name]);
                }
            }

            $items[] = $item;

        }

        return $items;

    }

    public function getHighlightedText($text){

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

        if (!$found_sentences) { return false; }

        $found_sentences = implode('... ', $found_sentences);

        return $found_sentences;

    }

}
