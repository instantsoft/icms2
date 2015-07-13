<?php

class fieldAge extends cmsFormField {

    public $title   = LANG_PARSER_AGE;
    public $sql     = 'datetime NULL DEFAULT NULL';
    public $filter_type = 'date';

    public function getOptions(){
        return array(
            new fieldString('date_title', array(
                'title' => LANG_PARSER_AGE_DATE_TITLE,
                'rules' => array( array('required') )
            )),
            new fieldCheckbox('show_y', array(
                'title' => LANG_YEARS
            )),
            new fieldCheckbox('show_m', array(
                'title' => LANG_MONTHS
            )),
            new fieldCheckbox('show_d', array(
                'title' => LANG_DAYS
            )),
            new fieldCheckbox('show_h', array(
                'title' => LANG_HOURS
            )),
            new fieldCheckbox('show_i', array(
                'title' => LANG_MINUTES
            )),
            new fieldList('range', array(
                'title' => LANG_PARSER_AGE_FILTER_RANGE,
                'items' => array(
                    'YEAR' => LANG_YEARS,
                    'MONTH' => LANG_MONTHS,
                    'DAY' => LANG_DAYS,
                )
            )),
        );
    }

    public function parse($value){
        return htmlspecialchars( $this->getDiff($value) );
    }

    public function getDiff($date){

        $options = array();

        if ($this->getOption('show_y')){ $options[] = 'y'; }
        if ($this->getOption('show_m')){ $options[] = 'm'; }
        if ($this->getOption('show_d')){ $options[] = 'd'; }
        if ($this->getOption('show_h')){ $options[] = 'h'; }
        if ($this->getOption('show_i')){ $options[] = 'i'; }

        return string_date_age($date, $options);

    }

    public function getFilterInput($value) {

        $from = !empty($value['from']) ? intval($value['from']) : false;
        $to = !empty($value['to']) ? intval($value['to']) : false;

        $range = constant('LANG_' . $this->getOption('range').'10');

        return LANG_FROM . ' ' . html_input('text', $this->element_name.'[from]', $from, array('class'=>'input-small')) . ' ' .
               LANG_TO . ' ' . html_input('text', $this->element_name.'[to]', $to, array('class'=>'input-small')) . ' ' .
               $range;

    }

    public function applyFilter($model, $value) {

        if (!is_array($value)) { return $model; }

        if (!empty($value['from'])){
            $from = intval($value['from']);
            $model->filterDateOlder($this->name, $from, $this->getOption('range'));
        }

        if (!empty($value['to'])){
            $to = intval($value['to']);
            $model->filterDateYounger($this->name, $to, $this->getOption('range'));
        }

        return $model;

    }

    public function store($value, $is_submitted, $old_value=null){

        $config = cmsConfig::getInstance();

        if ($value){
            $date = DateTime::createFromFormat($config->date_format, $value);
            return $date->format('Y-m-d');
        } else {
            return null;
        }

    }

}
