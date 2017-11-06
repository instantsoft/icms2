<?php

class fieldDate extends cmsFormField {

    public $title       = LANG_PARSER_DATE;
    public $sql         = 'timestamp NULL DEFAULT NULL';
    public $filter_type = 'date';
    public $filter_hint = LANG_PARSER_DATE_FILTER_HINT;
    public $var_type    = 'string';

    public function getOptions(){
        return array(
            new fieldCheckbox('show_time', array(
                'title' => LANG_PARSER_DATE_SHOW_TIME,
                'default' => false
            )),
            new fieldCheckbox('filter_range', array(
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => true
            )),
        );
    }

    public function parse($value){
        return $value ? html_date($value, $this->getOption('show_time')) : null;
    }

    public function getFilterInput($value) {

        if ($this->getOption('filter_range')){

            $from = !empty($value['from']) ? date('d.m.Y', strtotime($value['from'])) : false;
            $to = !empty($value['to']) ? date('d.m.Y', strtotime($value['to'])) : false;

            $this->title = false;

            return cmsTemplate::getInstance()->renderFormField($this->class."_range", array(
                'field' => $this,
                'from' => $from,
                'to' => $to
            ));


        } else {

            return parent::getFilterInput($value);

        }

    }

    public function applyFilter($model, $value) {

        if (!is_array($value) || !empty($value['date'])){

            if(!empty($value['date'])){
                $value = sprintf('%s %02d:%02d', $value['date'], $value['hours'], $value['mins']);
            }

            $date_start = date('Y-m-d', strtotime($value));
            $date_final = date('Y-m-d', strtotime($value)+60*60*24);

            return $model->filterBetween($this->name, $date_start, $date_final);

        } elseif(!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])){
                $model->filterGtEqual($this->name, date('Y-m-d', strtotime($value['from'])));
            }
            if (!empty($value['to'])){
                $model->filterLtEqual($this->name, date('Y-m-d', strtotime($value['to'])+60*60*24));
            }

            return $model;

        }

        return parent::applyFilter($model, $value);

    }

    public function getDefaultVarType($is_filter=false) {

        if (($is_filter && $this->getOption('filter_range')) || $this->getOption('show_time')){
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);

    }

    public function store($value, $is_submitted, $old_value=null){

        if($value){
            if(is_array($value)){
                if(!empty($value['date'])){
                    $value = sprintf('%s %02d:%02d', $value['date'], $value['hours'], $value['mins']);
                    return date('Y-m-d H:i:s', strtotime($value));
                }
            } else {
                return date('Y-m-d', strtotime($value));
            }

        }

        return null;

    }

    public function getInput($value){

        if($value){
            if(is_array($value)){
                if(!empty($value['date'])){
                    $value = sprintf('%s %02d:%02d', $value['date'], $value['hours'], $value['mins']);
                }
            }
        }

        $this->data['show_time'] = $this->getOption('show_time');

        $this->data['date'] = $value ? date('d.m.Y', strtotime($value)) : '';

        if($this->data['show_time']){
            if(!$value){
                $this->data['hours'] = 0;
                $this->data['mins'] = 0;
            }else{
                list($this->data['hours'], $this->data['mins']) = explode(':', date('H:i', strtotime($value)));
            }
            $this->data['fname_date']   = $this->element_name.'[date]';
            $this->data['fname_hours']  = $this->element_name.'[hours]';
            $this->data['fname_mins']   = $this->element_name.'[mins]';
        }else{
            $this->data['fname_date']   = $this->element_name;
        }

        return parent::getInput($value);

    }

}
