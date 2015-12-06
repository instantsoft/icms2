<?php

class fieldNumber extends cmsFormField {

    public $title   = LANG_PARSER_NUMBER;
    public $sql     = 'float NULL DEFAULT NULL';
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

        return $this->rules;

    }

    public function parse($value){
        $units = $this->getProperty('units');
        if(!$units) { $units = $this->getOption('units'); }
        if(!$units) { $units = ''; }
		if (intval($value)==$value){ $value = number_format($value, 0, '.', ''); }
        return htmlspecialchars($value)." {$units}";
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

        if (!$this->getOption('filter_range')){

            $model->filterEqual($this->name, "{$value}");

        } else {

            if (!is_array($value)) { return $model; }

            if (!empty($value['from'])){
                $model->filterGtEqual($this->name, $value['from']);
            }
            if (!empty($value['to'])){
                $model->filterLtEqual($this->name, $value['to']);
            }

        }

        return $model;

    }

    public function store($value, $is_submitted, $old_value=null){

        return str_replace(',', '.', trim($value));

    }

    public function getInput($value){

        $this->data['units'] = $this->getProperty('units')?:$this->getOption('units');

        return parent::getInput($value);

    }

}
