<?php

class fieldString extends cmsFormField {

    public $title       = LANG_PARSER_STRING;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $var_type    = 'string';
    public $type        = 'text';

    public function getOptions(){
        return array(
            new fieldNumber('min_length', array(
                'title' => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            )),
            new fieldNumber('max_length', array(
                'title'   => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 255,
                'rules' => array(
                    array('min', 1)
                )
            )),
            new fieldCheckbox('show_symbol_count', array(
                'title' => LANG_PARSER_SHOW_SYMBOL_COUNT
            )),
            new fieldCheckbox('is_autolink', array(
                'title' => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'  => LANG_PARSER_LIST_IS_AUTOLINK_HINT.LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default' => false
            ))
        );
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

        if ($this->getOption('is_autolink')){

            return html_search_bar($value, href_to($this->item['ctype_name']).'?'.$this->name.'=', 'string_autolink '.$this->item['ctype_name'].'_string_autolink');

        }

        return htmlspecialchars($value);

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value=null){
        if($this->getProperty('is_clean_disable') === true){
            return trim($value);
        }
        return strip_tags(trim($value));
    }

    public function getStringValue($value){
        return $value;
    }

    public function getInput($value){

        $this->data['type']         = $this->getProperty('is_password') ? 'password' : $this->getProperty('type');
        $this->data['autocomplete'] = $this->getProperty('autocomplete');

        return parent::getInput($value);

    }

}
