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
            new fieldCheckbox('is_html_filter', array(
                'title' => LANG_PARSER_HTML_FILTERING,
            )),
            new fieldCheckbox('build_redirect_link', array(
                'title' => LANG_PARSER_BUILD_REDIRECT_LINK,
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

        if ($this->getOption('min_length')){
            $this->rules[] = array('min_length', $this->getOption('min_length'));
        }

        if ($this->getOption('max_length')){
            $this->rules[] = array('max_length', $this->getOption('max_length'));
        }

        return $this->rules;

    }

    public function parse($value){

        if ($this->getOption('is_html_filter')){
            return cmsEventsManager::hook('html_filter', array(
                'text'                => $value,
                'is_auto_br'          => true,
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ));
        } else {
            return nl2br(htmlspecialchars($value));
        }

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

}
