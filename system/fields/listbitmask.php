<?php

class fieldListBitmask extends cmsFormField {

    public $title       = LANG_PARSER_LIST_MULTIPLE;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $allow_index = true;
    public $filter_type = 'str';
    public $var_type    = 'array';

    public function getOptions() {
        return [
            new fieldCheckbox('is_checkbox_multiple', [
                'title'   => LANG_PARSER_BITMASK_CHECKBOX_MULTIPLE,
                'default' => true
            ]),
            new fieldString('list_class', [
                'title'   => LANG_PARSER_BITMASK_LIST_CLASS,
                'default' => 'multiple_tags_list',
                'extended_option' => true
            ]),
            new fieldNumber('max_length', [
                'title'   => LANG_PARSER_BITMASK_MAX,
                'hint'    => LANG_PARSER_BITMASK_MAX_HINT,
                'default' => 64,
                'rules'   => [
                    ['min', 1]
                ]
            ]),
            new fieldCheckbox('is_autolink', [
                'title'   => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'    => LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default' => false,
                'extended_option' => true
            ])
        ];
    }

    public function setOptions($options){

        parent::setOptions($options);

        if (empty($this->items)){
            $this->items = $this->getListItems();
        }

        if ($this->hasDefaultValue() && strpos($this->default, "\n") !== false){
            $this->default = null;
        }
    }

    public function getFilterInput($value) {

        if (!$this->show_filter_input_title) {
            $this->element_title = '';
        }

        return $this->getInput($value);
    }

    public function getSelectedValues($value) {

        if (is_empty_value($value)) {
            return [];
        }

        $list = [];

        if ($this->items) {

            $pos = 0;

            $is_array = is_array($value);

            foreach ($this->items as $key => $name) {

                if (!$is_array) {

                    if (substr($value, $pos, 1) === '1') {
                        $list[$key] = $name;
                    }

                    $pos++;
                    if ($pos + 1 > strlen($value)) {
                        break;
                    }
                } else {

                    if (in_array($key, $value)) {
                        $list[$key] = $name;
                    }
                }
            }
        }

        return $list;
    }

    public function getStringValue($value) {

        $list = $this->getSelectedValues($value);

        if (!$list) {
            return '';
        }

        return implode(', ', $list);
    }

    public function parse($value) {

        $list = $this->getSelectedValues($value);

        if (!$list) {
            return '';
        }

        $html = '';

        $is_autolink = $this->getOption('is_autolink');

        $html .= '<ul class="' . $this->getOption('list_class') . ' list-unstyled">';

        foreach ($list as $key => $name) {

            if ($is_autolink) {
                $html .= '<li class="list-inline-item"><a class="listbitmask_autolink ' . $this->item['ctype_name'] . '_listbitmask_autolink" href="' . href_to($this->item['ctype_name']) . '?' . $this->name . '%5B%5D=' . urlencode($key) . '">' . html($name, false) . '</a></li>';
            } else {
                $html .= '<li class="list-inline-item"><span>' . html($name, false) . '</span></li>';
            }
        }

        $html .= '</ul>';

        return $html;
    }

    public function parseValue($values, $return_as_array = false) {

        if (!$values || !is_array($values)) {
            return '';
        }

        $value = $return_as_array ? [] : '';

        if ($this->items) {
            foreach ($this->items as $key => $title) {
                if ($return_as_array) {
                    if (in_array($key, $values)) {
                        $value[] = $key;
                    }
                } else {
                    $value .= in_array($key, $values) ? '1' : '0';
                }
            }
        }

        return $value;
    }

    public function store($value, $is_submitted, $old_value = null) {

        $value = $this->parseValue($value, ($this->context === 'filter'));

        if (is_string($value) && mb_strpos($value, '1') === false) {
            return '';
        }

        return $value;
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

    public function applyFilter($model, $values) {

        if (!is_array($values)) {
            return parent::applyFilter($model, $values);
        }

        $filter = $this->parseValue($values);
        if (!$filter) {
            return parent::applyFilter($model, $values);
        }

        $filter = str_replace('0', '_', $filter) . '%';

        return $model->filterLike($this->name, $filter);
    }

    public function getInput($value) {

        $this->items = array_keys_to_string_type($this->items);
        $this->data['selected'] = [];

        $this->data['items'] = $this->items;
        $this->data['selected'] = array_keys($this->getSelectedValues($value));

        return parent::getInput($value);
    }

    public function hookAfterUpdate($content_table_name, $field, $field_old, $model) {

        $items = $model->limit(false)->
                selectOnly('i.id')->
                select('i.'.$field_old['name'])->
                get($content_table_name, function ($item, $model)use ($field_old) {
            return $item[$field_old['name']];
        });

        if (!$items || trim($field_old['values']) === trim($field['values'])) {
            return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);
        }

        $old_rows = string_explode_list($field_old['values']);
        ksort($old_rows, SORT_NATURAL);
        $new_rows = string_explode_list($field['values']);
        ksort($new_rows, SORT_NATURAL);

        foreach ($items as $id => $item_value) {

            if (!$item_value) {
                continue;
            }

            // Формируем старый массив значений
            $old_item_values = [];

            $pos = 0;

            foreach ($old_rows as $key => $value) {
                if (substr($item_value, $pos, 1) === '1') {
                    $old_item_values[] = $key;
                }
                $pos++;
            }

            // Формируем новую битовую маску
            $new_item_value = '';

            foreach ($new_rows as $nkey => $title) {
                $new_item_value .= in_array($nkey, $old_item_values) ? '1' : '0';
            }

            if(strpos($new_item_value, '1') === false){
                $new_item_value = null;
            }

            // записываем обратно в базу
            $model->update($content_table_name, $id, [
                $field_old['name'] => $new_item_value
            ], true);
        }

        return parent::hookAfterUpdate($content_table_name, $field, $field_old, $model);
    }

}
