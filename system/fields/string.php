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
            new fieldList('in_filter_as', array(
                'title' => LANG_PARSER_STRING_DISPLAY_VARIANT,
                'hint'  => '<a href="#" onclick="return fieldStringLoadDefault(\''.cmsTemplate::getInstance()->href_to('ctypes', array('field_string_ajax', $this->name)).'\')" class="ajaxlink">'.LANG_PARSER_STRING_ENTER_DEFAULT.'</a>',
                'items' => array(
                    'input'     => LANG_PARSER_STRING,
                    'select'    => LANG_PARSER_STRING_SELECT,
                    'checkbox'  => LANG_PARSER_STRING_CHECKBOX
                )
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

        return html($value, false);

    }

    public function applyFilter($model, $value) {
        switch($this->getOption('in_filter_as')){
            case 'select':
                return $model->filterEqual($this->name, $value);
            case 'checkbox':
                if($value){ // работает и без этого
                    return $model->filterNotNull($this->name);
                }
                return $model;
            case 'input':
            default:
                return $model->filterLike($this->name, '%'.$value.'%');
        }
    }

    public function getFilterInput($value){
        if($this->getOption('in_filter_as') === 'select'){
            $this->data['items'] = array('' => '');
            if($this->hasDefaultValue()){
                $this->data['items'] = $this->data['items'] + string_explode_list($this->getDefaultValue(), true);
            }
        }
        return parent::getFilterInput($value);
    }

    public function store($value, $is_submitted, $old_value=null){
        if($this->getProperty('is_clean_disable') === true){
            return trim($value);
        }
        return strip_tags(trim($value));
    }

    public function storeFilter($value){
        return $this->store($value, false);
    }

    public function getStringValue($value){
        return $value;
    }

    public function getInput($value){

        $this->data['type']         = $this->getProperty('is_password') ? 'password' : $this->getProperty('type');
        $this->data['autocomplete'] = $this->getProperty('autocomplete');
        $this->data['attributes']   = $this->getProperty('attributes')?:array('autocomplete' => 'off');

        if($this->data['autocomplete']){
            if(empty($this->data['autocomplete']['data'])){
                $this->data['autocomplete']['data'] = false;
            }
            if(empty($this->data['autocomplete']['url'])){
                $this->data['autocomplete']['url'] = false;
            }
            if(empty($this->data['autocomplete']['multiple_separator'])){
                $this->data['autocomplete']['multiple_separator'] = ', ';
            }
        }

        $this->data['attributes']['id'] = $this->id;
        $this->data['attributes']['required'] = (array_search(array('required'), $this->getRules()) !== false);

        return parent::getInput($value);

    }

}
