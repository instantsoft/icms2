<?php

class fieldList extends cmsFormField {

    public $title                   = LANG_PARSER_LIST;
    public $sql                     = 'int NULL DEFAULT NULL';
    public $filter_type             = 'int';
    public $filter_hint             = LANG_PARSER_LIST_FILTER_HINT;
    public $var_type                = 'string';
    public $native_tag              = false;
    public $dynamic_list            = false;
    public $show_empty_value        = true;
    public $disable_array_key_rules = false;

    public function __clone() {

        if (isset($this->generator) || $this->getOption('list_where') === 'table') {

            $this->items = null;
        }
    }

    public function getOptions() {
        return [
            new fieldCheckbox('as_radio_btn', [
                'title'   => LANG_PARSER_LIST_AS_RADIO_BTN,
                'default' => false
            ]),
            new fieldCheckbox('filter_multiple', [
                'title'   => LANG_PARSER_LIST_FILTER_MULTI,
                'default' => false
            ]),
            new fieldCheckbox('filter_multiple_checkbox', [
                'title'          => LANG_PARSER_LIST_FILTER_MULTICH,
                'default'        => false,
                'visible_depend' => ['options:filter_multiple' => ['show' => ['1']]]
            ]),
            new fieldCheckbox('is_autolink', [
                'title'           => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'            => LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default'         => false,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_empty_value', [
                'title'   => LANG_PARSER_LIST_ADD_EMPTY,
                'default' => true
            ]),
            new fieldList('list_where', [
                'title' => LANG_PARSER_LIST_WHERE,
                'items' => [
                    ''  => LANG_PARSER_LIST_WHERE_PRE,
                    'table' => LANG_PARSER_LIST_WHERE_TBL
                ]
            ]),
            new fieldString('list_table', [
                'title' => LANG_TABLE,
                'visible_depend' => ['options:list_where' => ['show' => ['table']]]
            ]),
            new fieldString('list_where_cond', [
                'title' => LANG_PARSER_LIST_COND,
                'patterns_hint' => ['patterns' =>  [
                    'eq' => '=',
                    'gt' => '>',
                    'lt' => '<',
                    'ge' => '≥',
                    'le' => '≤',
                    'nn' => LANG_FILTER_NOT_NULL,
                    'ni' => LANG_FILTER_IS_NULL,
                    'lk' => LANG_FILTER_LIKE,
                    'ln' => LANG_FILTER_NOT_LIKE,
                    'lb' => LANG_FILTER_LIKE_BEGIN,
                    'lf' => LANG_FILTER_LIKE_END,
                    'dy' => LANG_FILTER_DATE_YOUNGER,
                    'do' => LANG_FILTER_DATE_OLDER
                ], 'wrap_symbols' => ['"','"']],
                'hint' => LANG_PARSER_LIST_COND_HINT,
                'visible_depend' => ['options:list_where' => ['show' => ['table']]]
            ]),
            new fieldString('list_order', [
                'title' => LANG_SORTING,
                'hint' => LANG_PARSER_LIST_ORDER,
                'visible_depend' => ['options:list_where' => ['show' => ['table']]]
            ]),
            new fieldString('list_where_id', [
                'title' => LANG_PARSER_LIST_WHERE_ID,
                'visible_depend' => ['options:list_where' => ['show' => ['table']]]
            ]),
            new fieldString('list_where_title', [
                'title' => LANG_PARSER_LIST_WHERE_TITLE,
                'visible_depend' => ['options:list_where' => ['show' => ['table']]]
            ]),
            new fieldList('list_sorting', [
                'title' => LANG_SORTING,
                'default' => 'keys',
                'items' => [
                    ''       => LANG_SORTING_BYORDER,
                    'keys'   => LANG_PARSER_LIST_SORT_BY_KEYS,
                    'values' => LANG_PARSER_LIST_SORT_BY_VALUES
                ],
                'visible_depend' => ['options:list_where' => ['show' => ['']]]
            ])
        ];
    }

    public function setOptions($options) {

        parent::setOptions($options);

        if (array_key_exists('show_empty_value', $this->options)) {
            $this->show_empty_value = $this->options['show_empty_value'];
        }
    }

    public function getFilterInput($value) {

        if (!$this->show_filter_input_title) {
            $this->title = false;
        }

        if (empty($this->options['filter_multiple'])) {

            $this->setProperty('show_empty_value', true);

            return parent::getFilterInput($value);
        } else {

            $value = is_array($value) ? $value : [];

            $this->setProperty('as_radio_btn', false);
            $this->setOption('as_radio_btn', false);

            if (!empty($this->options['filter_multiple_checkbox'])) {

                $this->setProperty('is_multiple', true);

                // Если уже сформирорвали список, удаляем первое пустое
                if($this->show_empty_value && isset($this->items)){
                    unset($this->items['']);
                }

                $this->setProperty('show_empty_value', false);
            } else {

                $this->setProperty('is_chosen_multiple', true);
            }

            return parent::getFilterInput($value);
        }
    }

    public function getRules() {

        if ($this->disable_array_key_rules) {
            return $this->rules;
        }

        if (!$this->dynamic_list) {
            $this->rules[] = ['array_key', $this->getListItems()];
        } else {
            $this->rules[] = ['array_key_dynamic'];
        }

        return $this->rules;
    }

    public function getStringValue($value) {

        if (is_empty_value($value)) {
            return '';
        }

        $items = $this->getListItems();
        $item  = [];

        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $val) {
            if (!is_array($val) && isset($items[$val])) {
                $item[] = $items[$val];
            }
        }

        return implode(', ', $item);
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) {
            $item = $items[$value];
        }

        if ($this->getOption('is_autolink')) {
            return '<a class="list_autolink ' . $this->item['ctype_name'] . '_list_autolink" href="' . href_to($this->item['ctype_name']) . '?' . $this->name . '=' . urlencode($value) . '">' . html($item, false) . '</a>';
        }

        return html($item, false);
    }

    public function getListValuesItems() {

        $items = [];

        if (isset($this->value_items)) {

            $items = $this->value_items;
        } else if (isset($this->values_generator)) {

            $generator = $this->values_generator;
            $items     = $generator($this->item);
        }

        return $items;
    }

    public function getDefaultVarType($is_filter = false) {

        if ($this->context === 'filter') {
            $is_filter = true;
        }

        if ($is_filter && $this->getOption('filter_multiple')) {
            $this->var_type = 'array';
        }
        if ($this->getProperty('is_multiple')) {
            $this->var_type = 'array';
        }
        if ($this->getProperty('is_chosen_multiple')) {
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);
    }

    public function applyFilter($model, $value) {

        if (!is_array($value)) {

            return $model->filterEqual($this->name, $value);
        } else {

            return $model->filterIn($this->name, $value);
        }
    }

    public function getInput($value) {

        if ($this->getDefaultVarType() === 'array' && $value && !is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        if (is_array($value) && $value) {
            foreach ($value as $k => $v) {
                if (!is_array($v) && is_numeric($v)) {
                    $value[$k] = (int) $v;
                }
            }
        }

        $as_radio_btn = $this->getProperty('as_radio_btn')?:$this->getOption('as_radio_btn');

        $this->data['dom_attr'] = $this->getProperty('attributes') ?: [];
        $this->data['dom_attr']['id'] = $this->id;

        $this->data['items']                    = $this->getListItems();
        $this->data['is_multiple']              = $this->getProperty('is_multiple');
        $this->data['multiple_select_deselect'] = $this->getProperty('multiple_select_deselect');
        $this->data['is_chosen_multiple']       = $this->getProperty('is_chosen_multiple');
        $this->data['is_tree']                  = $this->getProperty('is_tree');
        $this->data['parent']                   = $this->getProperty('parent');
        $this->data['is_ns_value_items']        = false;
        $this->data['select_hint_if_empty']     = $this->getProperty('select_hint_if_empty') ?: LANG_SELECT;
        $this->data['select_hintmp_if_empty']   = $this->getProperty('select_hintmp_if_empty') ?: LANG_SELECT_MULTIPLE;

        if ($this->dynamic_list) {
            $this->data['value_items']       = $this->getListValuesItems();
            $first_value_item                = reset($this->data['value_items']);
            $this->data['is_ns_value_items'] = is_array($first_value_item);
            $this->class                     = 'list_dynamic';
            if (!$value) {
                $value = new stdClass();
            }
            if (!isset($this->multiple_keys)) {
                $this->multiple_keys = new stdClass();
            }
        } elseif($as_radio_btn) {
            $this->class = 'list_radio';
        }

        return parent::getInput($value);
    }

    public function validate_array_key_dynamic($value) {

        if (empty($value)) {
            return true;
        }

        if (!is_array($value)) {
            return ERR_VALIDATE_INVALID;
        }

        $items = [
            // Еще может быть ячейка field_value, в ней обычный input
            'field'        => $this->getListItems(),
            'field_select' => $this->getListValuesItems()
        ];

        // Если разбиты по группам
        // избавляемся от вложенности
        if ($items['field_select']) {
            $first_value_item = reset($items['field_select']);
            if (is_array($first_value_item)) {
                $field_select = [];
                foreach ($items['field_select'] as $fskey => $fsvalue) {
                    foreach ($fsvalue as $fsv_key => $fsv_value) {
                        $field_select[$fsv_key] = [$fsv_value];
                    }
                }
                $items['field_select'] = $field_select;
            }
        }

        if (isset($this->multiple_keys)) {
            foreach ($value as $val) {
                if (!is_array($val)) {
                    return ERR_VALIDATE_INVALID;
                }
                foreach ($this->multiple_keys as $name => $type) {
                    if (!array_key_exists($name, $val)) {
                        return ERR_VALIDATE_INVALID;
                    }
                    // Не пустой список
                    if (!empty($items[$type])) {
                        if (!isset($items[$type][$val[$name]])) {
                            return ERR_VALIDATE_INVALID;
                        }
                    }
                }
            }
            return true;
        } else {
            foreach ($value as $k => $val) {
                if (!$k) {
                    if (!isset($items['field'][0]) && !isset($items['field'][''])) {
                        return ERR_VALIDATE_INVALID;
                    }
                } else {
                    if (!isset($items['field'][$k])) {
                        return ERR_VALIDATE_INVALID;
                    }
                }
                if (is_array($val)) {
                    return ERR_VALIDATE_INVALID;
                }
                if (!isset($items['field_select'][$val])) {
                    return ERR_VALIDATE_INVALID;
                }
            }
            return true;
        }

        return ERR_VALIDATE_INVALID;
    }

}
