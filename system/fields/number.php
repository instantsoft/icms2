<?php

class fieldNumber extends cmsFormField {

    public $title       = LANG_PARSER_NUMBER;
    public $sql         = 'float NULL DEFAULT NULL';
    public $filter_type = 'int';

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_range', array(
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => false
            )),
            new fieldString('units', array(
                'title' => LANG_PARSER_NUMBER_UNITS,
            )),
        );
    }

    public function getRules() {

        $this->rules[] = array('number');
        $this->rules[] = array('max_length', 7);

        return $this->rules;

    }

    public function parse($value){
        $units = $this->getProperty('units');
        if(!$units) { $units = $this->getOption('units'); }
        if(!$units) { $units = ''; }
		if (intval($value)==$value){ $value = number_format($value, 0, '.', ''); }
        return htmlspecialchars($value)." {$units}";
    }

    public function getDefaultVarType($is_filter=false) {

        if ($is_filter && $this->getOption('filter_range')){
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);

    }

    public function getFilterInput($value) {

        $units = $this->getProperty('units');
        if(!$units) { $units = $this->getOption('units'); }
        if(!$units) { $units = ''; }

        if ($this->getOption('filter_range')){

            $from = !empty($value['from']) ? intval($value['from']) : false;
            $to = !empty($value['to']) ? intval($value['to']) : false;

            return LANG_FROM . ' ' . html_input('text', $this->element_name.'[from]', $from, array('class'=>'input-small')) . ' ' .
                    LANG_TO . ' ' . html_input('text', $this->element_name.'[to]', $to, array('class'=>'input-small')) .
                    ($units ? ' ' . $units : '');

        } else {

            return parent::getFilterInput($value);

        }

    }

    public function applyFilter($model, $value) {

        if (!is_array($value)){

            return $model->filterEqual($this->name, "{$value}");

        } elseif(!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])){
                $model->filterGtEqual($this->name.'+0', $value['from']);
            }
            if (!empty($value['to'])){
                $model->filterLtEqual($this->name.'+0', $value['to']);
            }

            return $model;

        }

        return parent::applyFilter($model, $value);

    }

    public function store($value, $is_submitted, $old_value=null){

        return str_replace(',', '.', trim($value));

    }

    public function getInput($value){

        $this->data['units'] = $this->getProperty('units')?:$this->getOption('units');

        return parent::getInput($value);

    }

}