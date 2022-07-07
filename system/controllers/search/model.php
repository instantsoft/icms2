<?php

class modelSearch extends cmsModel {

    /**
     * Подготовленный массив слов запроса
     *
     * @var array
     */
    protected $query = [];

    /**
     * Оригинальная фраза запроса
     *
     * @var string
     */
    protected $original_query = '';

    /**
     * Массив полей, в которых нужно искать
     *
     * @var array
     */
    protected $match_fields = [];

    /**
     * Массив полей, в которых надо подсветить слова запроса
     *
     * @var array
     */
    protected $highlight_fields = [];

    /**
     * Подготовленная строка полей поиска
     *
     * @var string
     */
    protected $match_fields_str = '';

    /**
     * Тип поиска: words, exact
     *
     * @var string
     */
    protected $type = 'words';

    /**
     * Флаг поиска по трём символам
     *
     * @var boolean
     */
    protected $three_symbol_search = false;

    /**
     * Устанавливает тип поиска
     *
     * @param string $type
     * @return $this
     */
    public function setSearchType($type) {

        $this->type = $type;

        return $this;
    }

    /**
     * Устанавливает поля для поиска
     *
     * @param array $match_fields_list
     * @return $this
     */
    public function setMatchFields($match_fields_list) {

        $this->highlight_fields = $match_fields_list;
        $this->match_fields = $match_fields_list;
        $this->match_fields_str = 'i.' . implode(', i.', $match_fields_list);

        return $this;
    }

    /**
     * Устанавливает поля, в которых надо подсветить слова запроса
     *
     * @param array $highlight_fields
     * @return $this
     */
    public function setHighlightFields($highlight_fields) {

        $this->highlight_fields = $highlight_fields;

        return $this;
    }

    /**
     * Истина, если поиск по трём символам
     *
     * @return boolean
     */
    public function isThreeSymbolSearch() {
        return $this->three_symbol_search;
    }

    /**
     * Подготавливается поисковый запрос
     *
     * @param string $query
     * @return boolean
     */
    public function setQuery($query) {

        $query = trim(strip_tags(mb_strtolower(urldecode($query))));

        $query = trim(preg_replace('#['.preg_quote(implode('', $this->special_chars)).']+#', ' ', $query));

        $this->original_query = $query;

        $this->query = [];

        $stopwords = string_get_stopwords(cmsCore::getLanguageName());

        if (mb_strlen($query) === 3) {

            if (!$stopwords || ($stopwords && !in_array($query, $stopwords, true))) {

                // Узнаём минимальное кол-во символов для поиска
                // ft_min_word_len для MyISAM, innodb_ft_min_token_size для InnoDB
                $min_word_len_var = 'ft_min_word_len';
                if(cmsConfig::get('db_engine') === 'InnoDB' && cmsConfig::get('innodb_full_text')){
                    $min_word_len_var = 'innodb_ft_min_token_size';
                }

                $var_value = $this->db->getSqlVariableValue($min_word_len_var);

                if($var_value < 3){
                    $this->three_symbol_search = true;
                }

                $this->query[] = $query;

                return true;
            }

            return false;
        }

        $words = preg_split('/[\s,]+/', $query);

        foreach ($words as $word) {

            if (mb_strlen($word) < 3) {
                continue;
            }
            if ($stopwords && in_array($word, $stopwords, true)) {
                continue;
            }
            if (mb_strlen($word) === 3) {
                $this->query[] = $word;
                continue;
            }

            if (mb_strlen($word) >= 12) {
                $word = mb_substr($word, 0, mb_strlen($word) - 4);
            } else if (mb_strlen($word) >= 10) {
                $word = mb_substr($word, 0, mb_strlen($word) - 3);
            } else if (mb_strlen($word) >= 6) {
                $word = mb_substr($word, 0, mb_strlen($word) - 2);
            } else {
                $word = mb_substr($word, 0, mb_strlen($word) - 1);
            }

            $this->query[] = $word . '*';
        }

        if (empty($this->query)) {
            return false;
        }

        return true;
    }

    /**
     * Фильтр по периоду
     *
     * @param string $date_interval
     * @return $this
     */
    public function filterDateInterval($date_interval) {

        switch ($date_interval) {
            case 'w':
                return $this->filterDateYounger('date_pub', 7);
            case 'm':
                return $this->filterDateYounger('date_pub', 1, 'MONTH');
            case 'y':
                return $this->filterDateYounger('date_pub', 1, 'YEAR');
        }

        return $this;
    }

    /**
     * Фильтр по дополнительным параметрам от контроллеров
     *
     * @param array $filters
     * @return $this
     */
    public function filterSearch($filters) {

        if (!empty($filters['filters'])) {

            $this->applyDatasetFilters($filters, true);

            unset($filters['filters']);
        }

        foreach ($filters as $filter) {

            $filter['value'] = $this->db->prepareValue($filter['field'], $filter['value']);

            if (strpos($filter['field'], '.') === false){ $filter['field'] = 'i.' . $filter['field']; }

            $this->filter("{$filter['field']} {$filter['condition']} {$filter['value']}");
        }

        return $this;
    }

    /**
     * Фильтр по поисковому запросу
     *
     * @return $this
     */
    public function filterQuery() {

        $this->select = [];

        if ($this->three_symbol_search) {

            $query = $this->db->escape($this->original_query);

            if($this->type === 'words'){
                $query .= '%';
            }

            return $this->filter("CONCAT({$this->match_fields_str}) LIKE '{$query}'");
        }

        $query = '\"' . $this->db->escape($this->original_query) . '\"';

        if($this->type === 'words'){

            $query = '>\"' . $this->db->escape($this->original_query) . '\" <(';
            $query .= '+' . implode(' +', $this->db->escape($this->query)) . ')';
        }

        $search_param = "MATCH({$this->match_fields_str}) AGAINST ('{$query}' IN BOOLEAN MODE)";

        $this->select($search_param, 'fsort');

        return $this->filter($search_param);
    }

    /**
     * Присоединяем другие таблицы
     *
     * @param array $joins
     * @return $this
     */
    public function applyJoins($joins) {

        foreach ($joins as $method => $args) {
            call_user_func_array([$this, $method], $args);
        }

        return $this;
    }

    /**
     * Возвращает результат поиска
     *
     * @param string $table_name
     * @return array
     */
    public function getSearchResults($table_name) {

        return $this->get($table_name, function($item, $model) {

            foreach ($this->highlight_fields as $field_name) {
                $item[$field_name] = $this->getHighlightedText($item[$field_name]);
            }

            return $item;

        }, false) ?: [];
    }

    /**
     * Находит искомые слова в тексте и подсвечивает их
     *
     * @param string $text
     * @return string
     */
    public function getHighlightedText($text) {

        if(!$text){ return ''; }

        $text = str_replace('>', '> ', $text);
        $text = strip_tags($text);
        $text = preg_replace('#\s\s+#u', ' ', $text);

        $found_words     = [];
        $found_sentences = [];
        $found_sentences_counts = [];

        foreach ($this->query as $word) {

            $word = preg_quote(rtrim($word, '*'));

            if (preg_match("#\b({$word}\w+)\b#iu", $text, $matches)) {
                $found_words[] = $matches[0];
            }
        }

        if(mb_strlen($text) > 250){
            $sentences = explode('.', $text);
        } else {
            $sentences = [$text];
        }

        $sentences = array_map(function ($s) {
            return trim($s);
        }, $sentences);

        foreach ($sentences as $sentence) {

            $is_found = false;

            $count = 0;

            $sentence_lower = mb_strtolower($sentence);

            foreach ($found_words as $word) {

                $wcount = substr_count($sentence_lower, mb_strtolower($word));

                if ($wcount) {
                    $sentence = str_replace($word, '<em>' . $word . '</em>', $sentence);
                    $is_found = true;
                }

                $count += $wcount;
            }

            if ($is_found) {
                $found_sentences[] = $sentence;
                $found_sentences_counts[] = $count;
            }
        }

        if (!$found_sentences) {
            return string_short($text, 250);
        }

        // Сортируем и оставляем только 3 предложения
        arsort($found_sentences_counts);
        $found_sentences_counts = array_slice($found_sentences_counts, 0, 3);

        $text_list = [];

        foreach ($found_sentences_counts as $key => $value) {
            $text_list[] = $found_sentences[$key];
        }

        return implode('. ', $text_list);
    }

}
