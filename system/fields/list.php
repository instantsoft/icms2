<?php

class fieldList extends cmsFormField {

    public $title       = LANG_PARSER_LIST;
    public $sql         = 'int NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_LIST_FILTER_HINT;
    public $var_type    = 'string';
    public $native_tag  = false;

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_multiple', array(
                'title' => LANG_PARSER_LIST_FILTER_MULTI,
                'default' => false
            )),
            new fieldCheckbox('is_autolink', array(
                'title' => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'  => LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default' => false
            ))
        );
    }

    public function getFilterInput($value) {

        $items = $this->getListItems(false);

         if (!$this->getOption('filter_multiple')){

            $items = array(''=>'') + $items;
            return html_select($this->name, $items, $value);

         } else {

             $value = is_array($value) ? $value : array();
             return html_select_multiple($this->name, $items, $value);

         }

    }

    public function getRules() {

        if($this->item){
            $this->rules[] = array('array_key', $this->getListItems());
        }

        return $this->rules;

    }

    public function getStringValue($value){

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) { $item = $items[$value]; }

        return $item;

    }

    public function parse($value){

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) { $item = $items[$value]; }

        if ($this->getOption('is_autolink')){
            return '<a class="list_autolink '.$this->item['ctype_name'].'_list_autolink" href="'.href_to($this->item['ctype_name']).'?'.$this->name.'='.urlencode($value).'">'.htmlspecialchars($item).'</a>';
        }

        return htmlspecialchars($item);

    }

    public function getListItems($show_empty_value = true){

        $items = array();

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = ($show_empty_value ? array('' => '') : array()) + $this->parseListItems($this->getDefaultValue());

        }

        return $items;

    }

    public function parseListItems($string){
        return string_explode_list($string);
    }

    public function getDefaultVarType($is_filter=false) {

        if ($is_filter && $this->getOption('filter_multiple')){
            $this->var_type = 'array';
        }
        if($this->getProperty('is_multiple')){
            $this->var_type = 'array';
        }
        if($this->getProperty('is_chosen_multiple')){
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);

    }

    public function applyFilter($model, $value) {

        if (!is_array($value)){

            return $model->filterEqual($this->name, $value);

        } else {

            return $model->filterIn($this->name, $value);

        }

    }

    public function getInput($value){

        if($this->getDefaultVarType() === 'array' && $value && !is_array($value)){
            $value = cmsModel::yamlToArray($value);
        }

        $this->data['items']       = $this->getListItems();
        $this->data['is_multiple'] = $this->getProperty('is_multiple');
        $this->data['multiple_select_deselect'] = $this->getProperty('multiple_select_deselect');
        $this->data['is_chosen_multiple'] = $this->getProperty('is_chosen_multiple');
        $this->data['is_tree']     = $this->getProperty('is_tree');
        $this->data['parent']      = $this->getProperty('parent');
        $this->data['dom_attr']    = array('id' => $this->id);

        return parent::getInput($value);

    }

}
