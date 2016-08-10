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
        );
    }

    public function getFilterInput($value) {

        $items = $this->getListItems();

         if (!$this->getOption('filter_multiple')){

            $items = array(''=>'') + $items;// array_pad($items, (sizeof($items)+1)*-1, '');
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

    public function parse($value){

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) { $item = $items[$value]; }

        return htmlspecialchars($item);

    }

    public function getListItems(){

        $items = array();

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = $this->parseListItems($this->getDefaultValue());

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

        $this->data['items']       = $this->getListItems();
        $this->data['is_multiple'] = $this->getProperty('is_multiple');
        $this->data['is_tree']     = $this->getProperty('is_tree');
        $this->data['parent']      = $this->getProperty('parent');
        $this->data['dom_attr']    = array('id' => $this->id);

        return parent::getInput($value);

    }

}
