<?php

class fieldAge extends cmsFormField {

    public $title       = LANG_PARSER_AGE;
    public $sql         = 'datetime NULL DEFAULT NULL';
    public $filter_type = 'date';
    public $filter_hint = LANG_PARSER_DATE_FILTER_HINT;
    public $var_type    = 'string';

    public function getOptions() {
        return array(
            new fieldString('date_title', [
                'title' => LANG_PARSER_AGE_DATE_TITLE,
                'can_multilanguage' => true,
                'rules' => [['required']]
            ]),
            new fieldCheckbox('show_date', [
                'title' => LANG_PARSER_DATE_SHOW_DATE
            ]),
            new fieldCheckbox('show_y', [
                'title' => LANG_YEARS,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_m', [
                'title' => LANG_MONTHS,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_d', [
                'title' => LANG_DAYS,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_h', [
                'title' => LANG_HOURS,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_i', [
                'title' => LANG_MINUTES,
                'extended_option' => true
            ]),
            new fieldList('range', [
                'title' => LANG_PARSER_AGE_FILTER_RANGE,
                'items' => [
                    'YEAR'  => LANG_YEARS,
                    'MONTH' => LANG_MONTHS,
                    'DAY'   => LANG_DAYS,
                ]
            ]),
            new fieldDate('from_date', [
                'title' => LANG_PARSER_AGE_FROM_DATE,
                'hint'  => LANG_PARSER_AGE_FROM_DATE_HINT,
                'extended_option' => true
            ])
        );
    }

    public function getRules() {

        if ($this->context === 'filter') {
            $this->rules[] = ['age_range'];
        } else {
            $this->rules[] = ['date'];
        }

        return $this->rules;
    }

    public function getDefaultVarType($is_filter = false) {

        if ($this->context === 'filter') {
            $is_filter = true;
        }

        if ($is_filter) {
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);
    }

    public function parse($value) {

        if (!$value){
            return '';
        }

        if($this->getOption('show_date')){
            return html($this->getDiff($value), false).' ('. html_date($value).')';
        }

        return html($this->getDiff($value), false);
    }

    public function getStringValue($value) {

        if (!$value) {
            return '';
        }

        if (is_array($value)) {

            $result_string = '';

            if (!empty($value['from'])) {
                $result_string .= LANG_FROM . ' ' . $value['from'] . ' ';
            }

            if (!empty($value['to'])) {
                $result_string .= LANG_TO . ' ' . $value['to'];
            }

            return $result_string . ' ' . constant('LANG_' . $this->getOption('range') . '10');
        }

        return $this->getDiff($value);
    }

    public function getDiff($date) {

        $options = [];

        if ($this->getOption('show_y')) {
            $options[] = 'y';
        }
        if ($this->getOption('show_m')) {
            $options[] = 'm';
        }
        if ($this->getOption('show_d')) {
            $options[] = 'd';
        }
        if ($this->getOption('show_h')) {
            $options[] = 'h';
        }
        if ($this->getOption('show_i')) {
            $options[] = 'i';
        }
        if ($this->getOption('from_date')) {
            $options['from_date'] = $this->getOption('from_date');
        }

        return string_date_age($date, $options);
    }

    public function getFilterInput($value) {

        $from = !empty($value['from']) ? intval($value['from']) : false;
        $to   = !empty($value['to']) ? intval($value['to']) : false;

        if (!$this->show_filter_input_title) {
            $this->title = false;
        }

        return cmsTemplate::getInstance()->renderFormField($this->class . '_filter', [
            'field' => $this,
            'range' => constant('LANG_' . $this->getOption('range') . '10'),
            'from'  => $from,
            'to'    => $to
        ]);
    }

    public function applyFilter($model, $value) {

        if (!is_array($value)) {
            return parent::applyFilter($model, $value);
        }

        if (!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])) {
                $model->filterDateOlder($this->name, intval($value['from']), $this->getOption('range'));
            }

            if (!empty($value['to'])) {
                $model->filterTimestampYounger($this->name, $value['to'], $this->getOption('range'));
            }

            return $model;
        }

        return parent::applyFilter($model, $value);
    }

    public function store($value, $is_submitted, $old_value = null) {

        // Если ячейка в БД начинается на date_, то cmsDatabase в prepareValue
        // null будет CURRENT_TIMESTAMP
        $default_null = strpos($this->name, 'date_') === 0 ? 0 : null;

        if (!$value) {
            return $default_null;
        }

        if (!is_array($value)) {

            return date('Y-m-d H:i:s', strtotime($value));

        } elseif (isset($value['from']) || isset($value['to'])) {

            return [
                'from' => (isset($value['from']) ? $value['from'] : null),
                'to'   => (isset($value['to']) ? $value['to'] : null)
            ];
        }

        return $default_null;
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

    public function getInput($value) {

        $this->data['date'] = $value ? date('d.m.Y', strtotime($value)) : '';

        return parent::getInput($value);
    }

    public function validate_age_range($value) {

        if (empty($value)) {
            return true;
        }

        if (isset($value['from']) || isset($value['to'])) {

            $rgxp = "/^([0-9]+)$/i";

            if (!empty($value['from'])) {

                if (is_array($value['from'])) {
                    return ERR_VALIDATE_INVALID;
                }

                if (!preg_match($rgxp, $value['from'])) {
                    return ERR_VALIDATE_INVALID;
                }
            }

            if (!empty($value['to'])) {

                if (is_array($value['to'])) {
                    return ERR_VALIDATE_INVALID;
                }

                if (!preg_match($rgxp, $value['to'])) {
                    return ERR_VALIDATE_INVALID;
                }
            }

            return true;
        }

        return ERR_VALIDATE_INVALID;
    }

}
