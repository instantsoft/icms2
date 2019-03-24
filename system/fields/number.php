<?php

class fieldNumber extends cmsFormField {

    public $title       = LANG_PARSER_NUMBER;
    public $sql         = 'DECIMAL({decimal_m},{decimal_d}) {unsigned} NULL DEFAULT NULL';
    public $filter_type = 'int';

    public function getOptions(){
        return array(
            new fieldCheckbox('is_abs', array(
                'title'   => LANG_PARSER_NUMBER_IS_ABS,
                'default' => false
            )),
            new fieldCheckbox('save_zero', array(
                'title'   => LANG_PARSER_NUMBER_SAVE_ZERO,
                'default' => true
            )),
            new fieldNumber('decimal_int', array(
                'title'   => LANG_PARSER_NUMBER_DECIMAL_INT,
                'default' => 7,
                'rules' => array(
                    array('required'),
                    array('max', 35)
                )
            )),
            new fieldList('thousands_sep', array(
                'title'   => LANG_PARSER_NUMBER_THOUSANDS_SEP,
                'default' => ' ',
                'items' => array(
                    ' ' => LANG_SPACE,
                    ',' => LANG_COMMA,
                    '’' => LANG_APOSTROPHE,
                    '.' => LANG_DOT,
                    'another' => LANG_ANOTHER,
                )
            )),
                new fieldString('thousands_sep_another', array(
                    'title' => LANG_PARSER_NUMBER_THOUSANDS_SEP,
                    'visible_depend' => array('options:thousands_sep' => array('show' => array('another'))),
                    'options'=>array(
                        'max_length'=> 12
                    )
                )),
            new fieldCheckbox('is_ceil', array(
                'title'   => LANG_PARSER_NUMBER_IS_CEIL,
                'default' => false
            )),
            new fieldList('dec_point', array(
                'title'   => LANG_PARSER_NUMBER_DEC_POINT,
                'default' => '.',
                'items' => array(
                    '.' => LANG_DOT,
                    ',' => LANG_COMMA,
                    ' ' => LANG_SPACE,
                    'another' => LANG_ANOTHER,
                ),
                'visible_depend' => array('options:is_ceil' => array('hide' => array('1')))
            )),
                new fieldString('dec_point_another', array(
                    'title' => LANG_PARSER_NUMBER_DEC_POINT,
                    'visible_depend' => array('options:dec_point' => array('show' => array('another')),'options:is_ceil' => array('hide' => array('1'))),
                    'options'=>array(
                        'max_length'=> 12
                    )
                )),
            new fieldNumber('decimal_s', array(
                'title'   => LANG_PARSER_NUMBER_DECIMAL_S,
                'default' => 2,
                'rules' => array(
                    array('max', 30)
                ),
                'visible_depend' => array('options:is_ceil' => array('hide' => array('1')))
            )),
            new fieldCheckbox('trim_dec', array(
                'title'   => LANG_PARSER_NUMBER_TRIM_ZERO,
                'default' => true,
                'visible_depend' => array('options:is_ceil' => array('hide' => array('1')))
            )),
            new fieldCheckbox('filter_range', array(
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => false
            )),
            new fieldString('units', array(
                'title' => LANG_PARSER_NUMBER_UNITS,
            )),
            new fieldList('units_sep', array(
                'title'   => LANG_PARSER_NUMBER_UNITS_SEP,
                'default' => ' ',
                'items' => array(
                    ' ' => LANG_SPACE,
                    'another' => LANG_ANOTHER,
                )
            )),
                new fieldString('units_sep_another', array(
                    'title' => LANG_PARSER_NUMBER_UNITS_SEP,
                    'visible_depend' => array('options:units_sep' => array('show' => array('another'))),
                    'options'=>array(
                        'max_length'=> 12
                    )
                )),
        );
    }

    public function getRules() {

        $this->rules[] = array('number');

        return $this->rules;

    }

    public function getOption($key, $default = null) {

        switch($key){
            case 'decimal_s':
                if(parent::getOption('is_ceil')){
                    return 0;
                }
                break;
            case 'thousands_sep':
                if(parent::getOption('thousands_sep') === 'another'){
                    return parent::getOption('thousands_sep_another');
                }
                break;
            case 'dec_point':
                if(parent::getOption('dec_point') === 'another'){
                    return parent::getOption('dec_point_another');
                }
                break;
            case 'units_sep':
                if(parent::getOption('units_sep') === 'another'){
                    return parent::getOption('units_sep_another');
                }
                break;
            default:
                break;
        }

        return parent::getOption($key, $default);

    }

    public function getSQL() {

        $decimal_int = (int)$this->getOption('decimal_int');
        $decimal_s = (int)$this->getOption('decimal_s');

        return str_replace(array(
            '{decimal_m}',
            '{decimal_d}',
            '{unsigned}'
        ), array(
            ($decimal_int + $decimal_s),
            $decimal_s,
            ($this->getOption('is_abs') ? 'UNSIGNED' : '')
        ), $this->sql);

    }

    public function parse($value){

        $units = $this->getProperty('units')?:$this->getOption('units');

        return $this->formatFloatValue($value).$this->getOption('units_sep').$units;

    }

    public function getStringValue($value){

        if(!$value){ return ''; }

        $units = $this->getProperty('units')?:$this->getOption('units');

        if(is_array($value)){

            $result_string = '';

            if (!empty($value['from'])){
                $result_string .= LANG_FROM.' '.$this->getStringValue($value['from']).' ';
            }

            if (!empty($value['to'])){
                $result_string .= LANG_TO.' '.$this->getStringValue($value['to']);
            }

            return $result_string;

        }

        return $this->formatFloatValue($value).$this->getOption('units_sep').$units;

    }

    public function hookAfterUpdate($content_table_name, $field, $field_old, $model){

        $new_decimal_int = isset($field['options']['decimal_int']) ? $field['options']['decimal_int'] : 7;
        $new_decimal_s   = empty($field['options']['is_ceil']) ? (isset($field['options']['decimal_s']) ? $field['options']['decimal_s'] : 2) : 0;
        $new_unsigned    = isset($field['options']['is_abs']) ? $field['options']['is_abs'] : false;

        $old_decimal_int = isset($field_old['parser']->options['decimal_int']) ? $field_old['parser']->options['decimal_int'] : 7;
        $old_decimal_s   = empty($field_old['parser']->options['is_ceil']) ? (isset($field_old['parser']->options['decimal_s']) ? $field_old['parser']->options['decimal_s'] : 2) : 0;
        $old_unsigned    = isset($field_old['parser']->options['is_abs']) ? $field_old['parser']->options['is_abs'] : false;

        if($field_old['type'] == $field['type'] && (
                $new_decimal_int != $old_decimal_int ||
                $new_decimal_s != $old_decimal_s ||
                $new_unsigned != $old_unsigned
                )){

            $field_sql = str_replace(array(
                '{decimal_m}',
                '{decimal_d}',
                '{unsigned}'
            ), array(
                ($new_decimal_int + $new_decimal_s),
                $new_decimal_s,
                ($new_unsigned ? 'UNSIGNED' : '')
            ), $this->sql);

            $sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$field_old['name']}` `{$field_old['name']}` {$field_sql}";

            $model->db->query($sql);

        }

        return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);

    }

    public function getDefaultVarType($is_filter=false) {

        if ($is_filter && $this->getOption('filter_range')){
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);

    }

    public function getFilterInput($value) {

        $units = $this->getProperty('units')?:$this->getOption('units');

        if ($this->getOption('filter_range')){

            $from = !empty($value['from']) ? $value['from'] : false;
            $to = !empty($value['to']) ? $value['to'] : false;

            return LANG_FROM . ' ' . html_input('text', $this->element_name.'[from]', $from, array('class'=>'input-small')) . ' ' .
                    LANG_TO . ' ' . html_input('text', $this->element_name.'[to]', $to, array('class'=>'input-small')) .
                    ($units ? ' ' . $units : '');

        } elseif($value && !is_array($value)) {

            return parent::getFilterInput($value);

        }

        return parent::getFilterInput('');

    }

    public function applyFilter($model, $value) {

        if (!is_array($value)){

            return $model->filterEqual($this->name, $value);

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

    public function store($value, $is_submitted, $old_value = null){

        $value = str_replace(',', '.', trim($value));

        if(!$this->getOption('save_zero') && !$value){ return null; }

        return $this->getOption('is_abs') ? abs($value) : $value;

    }

    public function getInput($value){

        $this->data['units'] = $this->getProperty('units')?:$this->getOption('units');
        return parent::getInput(!empty($this->options['is_digits']) ? (int)$value : $value);

    }

    private function formatFloatValue($value){

        $dec_point = $this->getOption('dec_point');
        $decimal_s = $this->getOption('decimal_s');

        $value = number_format(($decimal_s ? (float)$value : (int)$value), $decimal_s, $dec_point, ($this->getOption('thousands_sep')?:''));

        if($decimal_s && $this->getOption('trim_dec')){
            return rtrim(rtrim($value, '0'), $dec_point);
        }

        return $value;

    }

}
