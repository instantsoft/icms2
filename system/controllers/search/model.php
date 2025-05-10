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
     * @return bool
     */
    public function setQuery(string $query) {

        $query = mb_strtolower(strip_tags(urldecode($query)));
        $query = preg_replace(
            ['#[' . preg_quote(implode('', self::SPECIAL_CHARS)) . ']+#u', '#\s+#u'],
            ' ',
            $query
        );
        $query = trim($query);

        if (!$query) {
            return false;
        }

        $this->original_query = $query;
        $this->query = [];

        $stopwords = array_flip(string_get_stopwords(cmsCore::getLanguageName()));

        if (mb_strlen($query) === 3) {

            if (isset($stopwords[$query])) {
                return false;
            }

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

        $words = preg_split('/[\s,]+/u', $query, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {

            if (isset($stopwords[$word])) {
                continue;
            }

            if (($stem_word = $this->stemWord($word))) {
                $this->query[] = $stem_word;
            }
        }

        return !empty($this->query);
    }

    /**
     * Небольшой стемминг
     *
     * @param string $word
     * @return ?string
     */
    private function stemWord(string $word) {

        $len = mb_strlen($word);

        if ($len < 3)   { return null; }
        if ($len >= 12) { return mb_substr($word, 0, $len - 4) . '*'; }
        if ($len >= 10) { return mb_substr($word, 0, $len - 3) . '*'; }
        if ($len >= 6)  { return mb_substr($word, 0, $len - 2) . '*'; }
        if ($len >= 5)  { return mb_substr($word, 0, $len - 1) . '*'; }

        return $word;
    }

    /**
     * Фильтр по периоду
     *
     * @param string $date_interval Тип интервала
     * @param bool $return_filter_str Вернуть селект и строку фильтра, false по умолчанию
     * @return $this
     */
    public function filterDateInterval($date_interval, $return_filter_str = false) {

        switch ($date_interval) {
            case 'w':
                $interval = 'DAY';
                $value = 7;
                break;
            case 'm':
                $interval = 'MONTH';
                $value = 1;
                break;
            case 'y':
                $interval = 'YEAR';
                $value = 1;
                break;
            default:
                return $return_filter_str ? '' : $this;
        }

        $filter_str = "i.date_pub >= DATE_SUB(NOW(), INTERVAL {$value} {$interval})";

        return $return_filter_str ? $filter_str : $this->filter($filter_str);
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
     * @param bool $return_filter_str Вернуть селект и строку фильтра, false по умолчанию
     * @return $this
     */
    public function filterQuery($return_filter_str = false) {

        if (!$return_filter_str) {
            $this->select = [];
        }

        if ($this->three_symbol_search) {

            $query = $this->db->escape($this->original_query);

            if ($this->type === 'words') {
                $query .= '%';
            }

            $search_param = "CONCAT({$this->match_fields_str}) LIKE '{$query}'";

            if ($return_filter_str) {
                return [[], $search_param];
            }

            return $this->filter($search_param);
        }

        $query = '\"' . $this->db->escape($this->original_query) . '\"';

        if ($this->type === 'words') {

            $query = '>\"' . $this->db->escape($this->original_query) . '\" <(';
            $query .= '+' . implode(' +', $this->db->escape($this->query)) . ')';
        }

        $search_param = "MATCH({$this->match_fields_str}) AGAINST ('{$query}' IN BOOLEAN MODE)";

        if ($return_filter_str) {
            return [['fsort' => $search_param], $search_param];
        }

        $this->select($search_param, 'fsort');

        return $this->filter($search_param);
    }

    /**
     * Возвращает массив селекта и строку фильтра
     *
     * @return array
     */
    public function getFilterQuery() {
        return $this->filterQuery(true);
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
     * @param ?string $text
     * @param int $max_length
     * @param int $max_sentences
     * @return string
     */
    public function getHighlightedText($text, int $max_length = 250, int $max_sentences = 3) {

        if (!$text) {
            return '';
        }

        // Очищаем и нормализуем текст
        $text = strip_tags(str_replace('>', '> ', $text));
        $text = preg_replace('#\s+#u', ' ', $text);
        $text = trim($text);

        if (!$text) {
            return '';
        }

        // Получаем найденные слова
        $found_words = [];

        foreach ($this->query as $word) {

            $word = preg_quote(rtrim($word, '*'), '#'); $matches = [];

            if (preg_match("#\b({$word}\w*)\b#iu", $text, $matches)) {
                $found_words[] = $matches[1];
            }
        }

        if (!$found_words) {
            return string_short($text, $max_length);
        }

        // Разделение на предложения
        $sentences = (mb_strlen($text) > $max_length)
            ? preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY)
            : [$text];

        $highlighted = [];

        foreach ($sentences as $sentence) {

            $original = mb_strtolower($sentence);
            $count    = 0;

            foreach ($found_words as $word) {

                $pattern = '#(' . preg_quote($word, '#') . ')#iu';

                $replaced = preg_replace($pattern, '<em>$1</em>', $sentence);

                if ($replaced !== null && $replaced !== $sentence) {
                    $sentence = $replaced;
                    $count += substr_count($original, mb_strtolower($word));
                }
            }

            if ($count > 0) {
                $highlighted[] = ['text' => $sentence, 'count' => $count];
            }
        }

        if (!$highlighted) {
            return string_short($text, $max_length);
        }

        // Сортировка и вывод топ-N предложений
        usort($highlighted, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        $top_sentences = array_slice(array_column($highlighted, 'text'), 0, $max_sentences);

        return implode(' ', $top_sentences);
    }

}
