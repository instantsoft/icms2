<?php

class fieldHtml extends cmsFormField {

    public $title       = LANG_PARSER_HTML;
    public $sql         = 'mediumtext';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';

    public function hasOptions(){ return true; }

    public function getOptions(){
        return array(
            new fieldList('editor', array(
                'title' => LANG_PARSER_HTML_EDITOR,
                'default' => cmsConfig::get('default_editor'),
                'generator' => function($item){
                    $items = array();
                    $editors = cmsCore::getWysiwygs();
                    foreach($editors as $editor){ $items[$editor] = $editor; }
                    return $items;
                }
            )),
            new fieldCheckbox('is_html_filter', array(
                'title' => LANG_PARSER_HTML_FILTERING,
            )),
            new fieldCheckbox('build_redirect_link', array(
                'title' => LANG_PARSER_BUILD_REDIRECT_LINK,
            )),
            new fieldNumber('teaser_len', array(
                'title' => LANG_PARSER_HTML_TEASER_LEN,
                'hint' => LANG_PARSER_HTML_TEASER_LEN_HINT,
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

    public function parse($value){

        if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', array(
                'text'                => $value,
                'is_auto_br'          => false,
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ));
        }

        return $value;

    }

    public function parseTeaser($value) {

        $max_len = $this->getOption('teaser_len');

        if ($max_len){

            $url = href_to($this->item['ctype']['name'], $this->item['slug'] . ".html");

            $value = string_short($value, $max_len);
            $value .= '<a class="read-more" href="'.$url.'">'.LANG_MORE.'</a>';

        }

        return $value;

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

}