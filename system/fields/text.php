<?php

class fieldText extends cmsFormField {

    public $title       = LANG_PARSER_TEXT;
    public $sql         = 'text';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';
    public $size        = 5;

    public function getOptions(){
        return array(
            new fieldNumber('min_length', array(
                'title' => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            )),
            new fieldNumber('max_length', array(
                'title' => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 4096
            )),
            new fieldCheckbox('show_symbol_count', array(
                'title' => LANG_PARSER_SHOW_SYMBOL_COUNT
            )),
            new fieldCheckbox('is_strip_tags', array(
                'title' => LANG_PARSER_IS_STRIP_TAGS
            )),
            new fieldCheckbox('is_html_filter', array(
                'title' => LANG_PARSER_HTML_FILTERING,
				'extended_option' => true
            )),
            new fieldCheckbox('parse_patterns', array(
                'title' => LANG_PARSER_PARSE_PATTERNS,
                'hint' => LANG_PARSER_PARSE_PATTERNS_HINT
            )),
            new fieldCheckbox('build_redirect_link', array(
                'title' => LANG_PARSER_BUILD_REDIRECT_LINK,
                'is_visible' => cmsController::enabled('redirect')
            )),
            new fieldList('teaser_len_choice', array(
                'title' => LANG_PARSER_HTML_TEASER_LEN,
                'items'     => [
                    'none'  => LANG_PARSER_HTML_TEASER_NONE,
                    'symbols' => LANG_PARSER_HTML_TEASER_SYMBOLS,
                    'words'  => LANG_PARSER_HTML_TEASER_WORDS
                ],
                'hint' => LANG_PARSER_HTML_TEASER_CHOICE
            )),
            new fieldNumber('teaser_len_words', array(
                'title' => LANG_PARSER_HTML_TEASER_QUANTITY_WORDS,
                'default' => 50,
                'visible_depend' => ['options:teaser_len_choice' => ['show' => ['words']]]
            )),
            new fieldNumber('teaser_len_symbols', array(
                'title' => LANG_PARSER_HTML_TEASER_QUANTITY_SYMBOLS,
                'default' => 100,
                'hint'  => LANG_PARSER_HTML_TEASER_SYMBOLS_HINT,
                'visible_depend' => ['options:teaser_len_choice' => ['show' => ['symbols']]]
            )),
            new fieldCheckbox('show_show_more', array(
                'title' => LANG_PARSER_SHOW_SHOW_MORE,
                'default' => false,
				'extended_option' => true
            )),
            new fieldCheckbox('in_fulltext_search', array(
                'title' => LANG_PARSER_IN_FULLTEXT_SEARCH,
                'hint'  => LANG_PARSER_IN_FULLTEXT_SEARCH_HINT,
                'default' => false
            ))
        );
    }

    public function getFilterInput($value) {
        return html_input('text', $this->name, $value);
    }

    public function getRules() {

        if ($this->getOption('min_length')) {
            $this->rules[] = ['min_length', $this->getOption('min_length')];
        }

        if ($this->getOption('max_length')) {
            $this->rules[] = ['max_length', $this->getOption('max_length')];
        }

        return $this->rules;
    }

    public function parseTeaser($value) {

        if (is_empty_value($value)) {
            return '';
        }

        if (!empty($this->item['is_private_item'])) {
            return '<p class="private_field_hint text-muted">'.$this->item['private_item_hint'].'</p>';
        }

        switch($choice) {
            case 'symbols':
                $max_len = $this->getOption('teaser_len_symbols');
                $value = string_short($value, $max_len);
                return $value."...";
                break;
           case 'words':
                $max_len = $this->getOption('teaser_len_words');
                //string_short() работает некорректно, либо я не разобрался как он работает ¯\_(ツ)_/¯. Метод лежит в string.helper.php
                //$value = string_short($value, $max_len, '...', 'w'); 
                $slice_string = explode(" ", $value);
                $slice_string = array_slice($slice_string, 0, $max_len);
                $value = implode(" ", $slice_string);
                $value .= '...';
                return $value;
                break;          
        }

        return parent::parseTeaser($value);
    }

    public function parse($value){

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('is_html_filter')){

            return cmsEventsManager::hook('html_filter', [
                'text'                => $value,
                'is_auto_br'          => true,
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ]);

        } else {

            if($this->getProperty('is_strip_tags') === true || $this->getOption('is_strip_tags')){
                return nl2br($value);
            }
            return nl2br(html($value, false));
        }
    }

    public function afterParse($value, $item){

        if (!$value){
            return '';
        }

        if ($this->getOption('parse_patterns')){
            $value = string_replace_keys_values_extended($value, $item);
        }

        return $value;
    }

    public function getStringValue($value){

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('parse_patterns') && !empty($this->item)){
            $value = string_replace_keys_values_extended($value, $this->item);
        }

        return trim(strip_tags($value));
    }

    public function store($value, $is_submitted, $old_value = null) {
        if (!$value) {
            return '';
        }
        if ($this->getProperty('is_strip_tags') === true || $this->getOption('is_strip_tags')) {
            return trim(strip_tags($value));
        }
        return parent::store($value, $is_submitted, $old_value);
    }

    public function storeFilter($value){
        return $this->store($value, false);
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function getInput($value){

        $this->data['attributes']               = $this->getProperty('attributes')?:[];
        $this->data['attributes']['rows']       = $this->getOption('size')?:$this->getProperty('size');
        $this->data['attributes']['id']         = $this->id;
        $this->data['attributes']['required']   = (array_search(['required'], $this->getRules()) !== false);

        return parent::getInput($value);
    }

}
