<?php

class fieldList extends cmsFormField {

    public $title       = LANG_PARSER_LIST;
    public $sql         = 'int NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_LIST_FILTER_HINT;
    public $var_type    = 'string';
    public $native_tag  = false;
    public $dynamic_list = false;
    public $show_empty_value = true;
    public $disable_array_key_rules = false;

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_multiple', array(
                'title' => LANG_PARSER_LIST_FILTER_MULTI,
                'default' => false
            )),
            new fieldCheckbox('filter_multiple_checkbox', array(
                'title' => LANG_PARSER_LIST_FILTER_MULTICH,
                'default' => false,
                'visible_depend' => array('options:filter_multiple' => array('show' => array('1')))
            )),
            new fieldCheckbox('is_autolink', array(
                'title' => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'  => LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default' => false,
				'extended_option' => true
            ))
        );
    }

    public function getFilterInput($value) {

        if(!$this->show_filter_input_title){
            $this->title = false;
        }

        if (!$this->getOption('filter_multiple')) {

            return parent::getFilterInput($value);

        } else {

            $value = is_array($value) ? $value : array();

            if ($this->getOption('filter_multiple_checkbox')) {
                $this->setProperty('is_multiple', true);
                $this->setProperty('show_empty_value', false);
            } else {
                $this->setProperty('is_chosen_multiple', true);
            }

            return parent::getFilterInput($value);

        }

    }

    public function getRules() {

        if($this->disable_array_key_rules){
            return $this->rules;
        }

        if(!$this->dynamic_list){
            $this->rules[] = ['array_key', $this->getListItems()];
        } else {
            $this->rules[] = ['array_key_dynamic'];
        }

        return $this->rules;
    }

    public function getStringValue($value){

        $items = $this->getListItems();
        $item  = array();

        if(!is_array($value)){
            $value = array($value);
        }

        foreach ($value as $val) {
            if (isset($items[$val])) { $item[] = $items[$val]; }
        }

        return implode(', ', $item);

    }

    public function parse($value){

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) { $item = $items[$value]; }

        if ($this->getOption('is_autolink')){
            return '<a class="list_autolink '.$this->item['ctype_name'].'_list_autolink" href="'.href_to($this->item['ctype_name']).'?'.$this->name.'='.urlencode($value).'">'.html($item, false).'</a>';
        }

        return html($item, false);

    }

    public function getListValuesItems(){

        $items = array();

        if (isset($this->value_items)){

            $items = $this->value_items;

        } else if (isset($this->values_generator)) {

            $generator = $this->values_generator;
            $items = $generator($this->item);

        }

        return $items;

    }

    public function getDefaultVarType($is_filter = false) {

        if($this->context == 'filter'){
            $is_filter = true;
        }

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

        if(is_array($value) && $value){
            foreach ($value as $k => $v) {
                if(!is_array($v) && is_numeric($v)){ $value[$k] = (int)$v; }
            }
        }

        $this->data['items']       = $this->getListItems();
        $this->data['is_multiple'] = $this->getProperty('is_multiple');
        $this->data['multiple_select_deselect'] = $this->getProperty('multiple_select_deselect');
        $this->data['is_chosen_multiple'] = $this->getProperty('is_chosen_multiple');
        $this->data['is_tree']     = $this->getProperty('is_tree');
        $this->data['parent']      = $this->getProperty('parent');
        $this->data['dom_attr']    = array('id' => $this->id);
        $this->data['is_ns_value_items'] = false;

        if($this->dynamic_list){
            $this->data['value_items'] = $this->getListValuesItems();
            $first_value_item = reset($this->data['value_items']);
            $this->data['is_ns_value_items'] = is_array($first_value_item);
            $this->class = 'list_dynamic';
            if(!$value){ $value = new stdClass(); }
            if(!isset($this->multiple_keys)){ $this->multiple_keys = new stdClass(); }
        }

        return parent::getInput($value);
    }

    public function validate_array_key_dynamic($value){

        if (empty($value)) { return true; }

        if (!is_array($value)) { return ERR_VALIDATE_INVALID; }

        $items = [
            // Еще может быть ячейка field_value, в ней обычный input
            'field' => $this->getListItems(),
            'field_select' => $this->getListValuesItems()
        ];

        // Если разбиты по группам
        // избавляемся от вложенности
        if($items['field_select']){
            $first_value_item = reset($items['field_select']);
            if(is_array($first_value_item)){
                $field_select = [];
                foreach ($items['field_select'] as $fskey => $fsvalue) {
                    foreach ($fsvalue as $fsv_key => $fsv_value) {
                        $field_select[$fsv_key] = [$fsv_value];
                    }
                }
                $items['field_select'] = $field_select;
            }
        }

        if(isset($this->multiple_keys)){
            foreach ($value as $val) {
                foreach ($this->multiple_keys as $name => $type) {
                    if(!array_key_exists($name, $val)){
                        return ERR_VALIDATE_INVALID;
                    }
                    // Не пустой список
                    if(!empty($items[$type])){
                        if(!isset($items[$type][$val[$name]])){
                            return ERR_VALIDATE_INVALID;
                        }
                    }
                }
            }
            return true;
        } else {
            foreach ($value as $k => $val) {
                if(!$k){
                    if(!isset($items['field'][0]) && !isset($items['field'][''])){
                        return ERR_VALIDATE_INVALID;
                    }
                } else {
                    if(!isset($items['field'][$k])){
                        return ERR_VALIDATE_INVALID;
                    }
                }
                if(!isset($items['field_select'][$val])){
                    return ERR_VALIDATE_INVALID;
                }
            }
            return true;
        }

        return ERR_VALIDATE_INVALID;

    }

}
