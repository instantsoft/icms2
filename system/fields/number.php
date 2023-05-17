<?php

class fieldNumber extends cmsFormField {

    public $title       = LANG_PARSER_NUMBER;
    public $sql         = 'DECIMAL({decimal_m},{decimal_d}) {unsigned} NULL DEFAULT NULL';
    public $filter_type = 'int';

    public function getOptions() {
        return [
            new fieldList('input_type', [
                'title'   => LANG_PARSER_NUMBER_TYPE,
                'default' => 'text',
                'items' => [
                    'text'   => 'text',
                    'number' => 'number'
                ]
            ]),
            new fieldCheckbox('is_abs', [
                'title'   => LANG_PARSER_NUMBER_IS_ABS,
                'default' => false
            ]),
            new fieldCheckbox('save_zero', [
                'title'   => LANG_PARSER_NUMBER_SAVE_ZERO,
                'default' => true
            ]),
            new fieldNumber('decimal_int', [
                'title'   => LANG_PARSER_NUMBER_DECIMAL_INT,
                'default' => 7,
                'rules' => [
                    ['required'],
                    ['min', 1],
                    ['max', 35]
                ],
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true,
                ]
            ]),
            new fieldList('thousands_sep', [
                'title'   => LANG_PARSER_NUMBER_THOUSANDS_SEP,
                'default' => ' ',
                'items' => [
                    ' ' => LANG_SPACE,
                    ',' => LANG_COMMA,
                    '’' => LANG_APOSTROPHE,
                    '.' => LANG_DOT,
                    'another' => LANG_ANOTHER,
                ]
            ]),
            new fieldString('thousands_sep_another', [
                'title' => LANG_PARSER_NUMBER_THOUSANDS_SEP,
                'visible_depend' => ['options:thousands_sep' => ['show' => ['another']]],
                'options' => [
                    'max_length' => 12
                ]
            ]),
            new fieldCheckbox('is_ceil', [
                'title'   => LANG_PARSER_NUMBER_IS_CEIL,
                'default' => false
            ]),
            new fieldList('dec_point', [
                'title'   => LANG_PARSER_NUMBER_DEC_POINT,
                'default' => '.',
                'items' => [
                    '.' => LANG_DOT,
                    ',' => LANG_COMMA,
                    ' ' => LANG_SPACE,
                    'another' => LANG_ANOTHER,
                ],
                'visible_depend' => ['options:is_ceil' => ['hide' => ['1']]]
            ]),
            new fieldString('dec_point_another', [
                'title' => LANG_PARSER_NUMBER_DEC_POINT,
                'visible_depend' => ['options:dec_point' => ['show' => ['another']], 'options:is_ceil' => ['hide' => ['1']]],
                'options' => [
                    'max_length' => 12
                ]
            ]),
            new fieldNumber('decimal_s', [
                'title'   => LANG_PARSER_NUMBER_DECIMAL_S,
                'default' => 2,
                'rules' => [
                    ['max', 30]
                ],
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true,
                ],
                'visible_depend' => ['options:is_ceil' => ['hide' => ['1']]]
            ]),
            new fieldCheckbox('trim_dec', [
                'title'   => LANG_PARSER_NUMBER_TRIM_ZERO,
                'default' => true,
                'visible_depend' => ['options:is_ceil' => ['hide' => ['1']]]
            ]),
            new fieldCheckbox('filter_range', [
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => false
            ]),
            new fieldCheckbox('filter_range_slide', [
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE_SLIDE,
                'default' => false,
                'visible_depend' => ['options:filter_range' => ['show' => ['1']]]
            ]),
            new fieldCheckbox('filter_range_show_input', [
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE_SI,
                'default' => false,
                'visible_depend' => ['options:filter_range' => ['show' => ['1']]]
            ]),
            new fieldNumber('filter_range_slide_step', [
                'title'   => LANG_PARSER_NUMBER_FILTER_STEP,
                'default' => 1,
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true,
                ],
                'visible_depend' => ['options:filter_range' => ['show' => ['1']]]
            ]),
            new fieldString('prefix', [
                'title' => LANG_PARSER_PREFIX,
                'can_multilanguage' => true
            ]),
            new fieldString('units', [
                'title' => LANG_PARSER_NUMBER_UNITS,
                'can_multilanguage' => true
            ]),
            new fieldList('units_sep', [
                'title'   => LANG_PARSER_NUMBER_UNITS_SEP,
                'default' => ' ',
                'items' => [
                    ' ' => LANG_SPACE,
                    'another' => LANG_ANOTHER,
                ]
            ]),
            new fieldString('units_sep_another', [
                'title' => LANG_PARSER_NUMBER_UNITS_SEP,
                'visible_depend' => ['options:units_sep' => ['show' => ['another']]],
                'options' => [
                    'max_length' => 12
                ]
            ])
        ];
    }

    public function getRules() {

        if ($this->context === 'filter' && $this->getOption('filter_range')) {

            // Если в настройках поля в админке указали "только целые числа"
            $rules_number_exists = array_search(['digits'], $this->rules);
            if ($rules_number_exists !== false) {
                unset($this->rules[$rules_number_exists]);
            }

            $this->rules[] = ['number_range'];
        } else {
            $this->rules[] = ['number'];
        }

        return parent::getRules();
    }

    public function getOption($key, $default = null) {

        switch ($key) {
            case 'decimal_s':
                if (parent::getOption('is_ceil')) {
                    return 0;
                }
                break;
            case 'thousands_sep':
                if (parent::getOption('thousands_sep') === 'another') {
                    return parent::getOption('thousands_sep_another');
                }
                break;
            case 'dec_point':
                if (parent::getOption('dec_point') === 'another') {
                    return parent::getOption('dec_point_another');
                }
                break;
            case 'units_sep':
                if (parent::getOption('units_sep') === 'another') {
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

        return str_replace([
            '{decimal_m}',
            '{decimal_d}',
            '{unsigned}'
        ], [
            ($decimal_int + $decimal_s),
            $decimal_s,
            ($this->getOption('is_abs') ? 'UNSIGNED' : '')
        ], $this->sql);
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        $units = $this->getProperty('units') ?: $this->getOption('units');
        $prefix = $this->getProperty('prefix') ?: $this->getOption('prefix', '');

        return $prefix . ' ' . $this->formatFloatValue($value) . $this->getOption('units_sep') . $units;
    }

    public function getStringValue($value) {

        if (is_empty_value($value)) {
            return '';
        }

        $units = $this->getProperty('units') ?: $this->getOption('units');

        if (is_array($value)) {

            $result_string = '';

            if (!empty($value['from'])) {
                $result_string .= LANG_FROM . ' ' . $this->getStringValue($value['from']) . ' ';
            }

            if (!empty($value['to'])) {
                $result_string .= LANG_TO . ' ' . $this->getStringValue($value['to']);
            }

            return $result_string;
        }

        return $this->formatFloatValue($value) . $this->getOption('units_sep') . $units;
    }

    public function hookAfterUpdate($content_table_name, $field, $field_old, $model) {

        $new_decimal_int = isset($field['options']['decimal_int']) ? $field['options']['decimal_int'] : 7;
        $new_decimal_s   = empty($field['options']['is_ceil']) ? (isset($field['options']['decimal_s']) ? $field['options']['decimal_s'] : 2) : 0;
        $new_unsigned    = isset($field['options']['is_abs']) ? $field['options']['is_abs'] : false;

        $old_decimal_int = isset($field_old['parser']->options['decimal_int']) ? $field_old['parser']->options['decimal_int'] : 7;
        $old_decimal_s   = empty($field_old['parser']->options['is_ceil']) ? (isset($field_old['parser']->options['decimal_s']) ? $field_old['parser']->options['decimal_s'] : 2) : 0;
        $old_unsigned    = isset($field_old['parser']->options['is_abs']) ? $field_old['parser']->options['is_abs'] : false;

        if ($field_old['type'] === $field['type'] && ($new_decimal_int != $old_decimal_int ||
            $new_decimal_s != $old_decimal_s ||
            $new_unsigned != $old_unsigned
        )) {

            $field_sql = str_replace([
                '{decimal_m}',
                '{decimal_d}',
                '{unsigned}'
            ], [
                ($new_decimal_int + $new_decimal_s),
                $new_decimal_s,
                ($new_unsigned ? 'UNSIGNED' : '')
            ], $this->sql);

            $sql = "ALTER TABLE `{#}{$content_table_name}` CHANGE `{$field_old['name']}` `{$field_old['name']}` {$field_sql}";

            $model->db->query($sql);
        }

        return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);
    }

    public function getDefaultVarType($is_filter = false) {

        if ($this->context === 'filter') {
            $is_filter = true;
        }

        if ($is_filter && $this->getOption('filter_range')) {
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);
    }

    public function getFilterInput($value) {

        if ($this->getOption('filter_range')) {

            $from = !empty($value['from']) ? $value['from'] : false;
            $to = !empty($value['to']) ? $value['to'] : false;

            if (!$this->show_filter_input_title) {
                $this->title = false;
            }

            $this->data['type'] = $this->getOption('input_type') ?: 'text';

            $this->data['units'] = $this->getProperty('units') ?: $this->getOption('units');

            $tpl_name = $this->class . '_range';

            if ($this->getOption('filter_range_slide') && strpos($this->name, ':') === false) {

                // получаем минимум и максимум
                if (!empty($this->item['ctype_name'])) {

                    $tpl_name = $this->class . '_range_slide';

                    $controller_name = 'content';

                    if (cmsCore::isModelExists($this->item['ctype_name'])) {
                        $controller_name = $this->item['ctype_name'];
                    }

                    $model = cmsCore::getModel($controller_name);

                    $max_value = $model->getMax($model->getContentTypeTableName($this->item['ctype_name']), $this->name, null);
                    $min_value = $model->getMin($model->getContentTypeTableName($this->item['ctype_name']), $this->name, null);

                    // Нет ничего, не показываем в фильтре
                    if ($max_value === null && $min_value === null) {
                        return '';
                    }
                    // одно значение
                    if ($max_value === $min_value) {
                        return '';
                    }

                    $this->data['slide_params'] = [
                        'min'  => $min_value,
                        'max'  => $max_value,
                        'step' => $this->getOption('filter_range_slide_step', 1),
                        'values' => [($from ?: $min_value), ($to ?: $max_value)]
                    ];
                }
            }

            return cmsTemplate::getInstance()->renderFormField($tpl_name, [
                'field' => $this,
                'from'  => $from,
                'to'    => $to
            ]);

        } elseif ($value && !is_array($value)) {

            return parent::getFilterInput($value);
        }

        return parent::getFilterInput('');
    }

    public function applyFilter($model, $value) {

        if (!is_array($value)) {

            return $model->filterEqual($this->name, $value);

        } elseif (!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])) {
                $model->filterGtEqual($this->name . '+0', $value['from']);
            }
            if (!empty($value['to'])) {
                $model->filterLtEqual($this->name . '+0', $value['to']);
            }

            return $model;
        }

        return parent::applyFilter($model, $value);
    }

    public function store($value, $is_submitted, $old_value = null) {

        if (!is_array($value)) {

            if (!$this->getOption('save_zero') && !$value) {
                return null;
            }

            $value = floatval(str_replace(',', '.', trim($value)));

            return $this->getOption('is_abs') ? abs($value) : $value;

        } elseif (!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])) {
                $value['from'] = $this->store($value['from'], $is_submitted, $old_value);
            }
            if (!empty($value['to'])) {
                $value['to'] = $this->store($value['to'], $is_submitted, $old_value);
            }

            return $value;
        }

        return null;
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

    public function getInput($value) {

        $this->data['type'] = $this->getOption('input_type') ?: 'text';

        $this->data['units'] = $this->getProperty('units') ?: $this->getOption('units');
        $this->data['prefix'] = $this->getProperty('prefix') ?: $this->getOption('prefix', '');

        $this->data['attributes']             = $this->getProperty('attributes') ?: [];
        $this->data['attributes']['size']     = ($this->getOption('size') ?: $this->getProperty('size')) ?: 5;
        $this->data['attributes']['id']       = $this->id;
        $this->data['attributes']['step']     = 'any';
        $this->data['attributes']['inputmode'] = $this->getOption('is_ceil') ? 'numeric' : 'decimal';
        $this->data['attributes']['required'] = (array_search(['required'], $this->getRules()) !== false);

        if(empty($this->data['attributes']['class'])){
            $this->data['attributes']['class'] = 'input-number';
        } else {
            $this->data['attributes']['class'] .= ' input-number';
        }

        return parent::getInput(!empty($this->options['is_digits']) ? (int)$value : $value);
    }

    private function formatFloatValue($value) {

        $dec_point = $this->getOption('dec_point');
        $decimal_s = $this->getOption('decimal_s');

        $value = number_format(($decimal_s ? (float)$value : (int)$value), $decimal_s, $dec_point, ($this->getOption('thousands_sep') ?: ''));

        if ($decimal_s && $this->getOption('trim_dec')) {
            return rtrim(rtrim($value, '0'), $dec_point);
        }

        return $value;
    }

    public function validate_number_range($value) {

        if (empty($value)) {
            return true;
        }

        if (!in_array(gettype($value), ['array'])) {
            return ERR_VALIDATE_NUMBER;
        }

        if (empty($value['from']) && empty($value['to'])) {
            return true;
        }

        if (!empty($value['from'])) {

            if (is_array($value['from'])) {
                return ERR_VALIDATE_NUMBER;
            }

            if (!is_numeric($value['from'])) {
                return ERR_VALIDATE_NUMBER;
            }
        }

        if (!empty($value['to']) && !is_array($value['to'])) {

            if (is_array($value['to'])) {
                return ERR_VALIDATE_NUMBER;
            }

            if (!is_numeric($value['to'])) {
                return ERR_VALIDATE_NUMBER;
            }
        }

        return true;
    }

}
