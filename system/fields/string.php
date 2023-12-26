<?php

class fieldString extends cmsFormField {

    public $title       = LANG_PARSER_STRING;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $var_type    = 'string';
    public $type        = 'text';

    public function getOptions() {
        return [
            new fieldNumber('min_length', [
                'title'   => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            ]),
            new fieldNumber('max_length', [
                'title'   => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 255,
                'rules'   => [
                    ['min', 1]
                ]
            ]),
            new fieldString('placeholder', [
                'title' => LANG_PARSER_PLACEHOLDER,
                'can_multilanguage' => true
            ]),
            new fieldCheckbox('use_inputmask', [
                'title' => LANG_PARSER_USE_INPUTMASK
            ]),
            new fieldString('inputmask_str', [
                'title'          => LANG_PARSER_INPUTMASK,
                'hint'           => LANG_PARSER_INPUTMASK_HINT,
                'visible_depend' => ['options:use_inputmask' => ['show' => ['1']]],
            ]),
            new fieldCheckbox('show_symbol_count', [
                'title' => LANG_PARSER_SHOW_SYMBOL_COUNT
            ]),
            new fieldList('in_filter_as', [
                'title' => LANG_PARSER_STRING_DISPLAY_VARIANT,
                'hint'  => '<a href="#" onclick="return fieldStringLoadDefault(\'' . cmsTemplate::getInstance()->href_to('ctypes', ['field_string_ajax', $this->name]) . '\')" class="ajaxlink">' . LANG_PARSER_STRING_ENTER_DEFAULT . '</a>',
                'items' => [
                    'input'    => LANG_PARSER_STRING,
                    'select'   => LANG_PARSER_STRING_SELECT,
                    'checkbox' => LANG_PARSER_STRING_CHECKBOX
                ]
            ]),
            new fieldNumber('teaser_len', [
                'title'           => LANG_PARSER_HTML_TEASER_LEN,
                'hint'            => LANG_PARSER_HTML_TEASER_LEN_HINT,
                'extended_option' => true
            ]),
            new fieldCheckbox('is_autolink', [
                'title'           => LANG_PARSER_LIST_IS_AUTOLINK,
                'hint'            => LANG_PARSER_LIST_IS_AUTOLINK_HINT . LANG_PARSER_LIST_IS_AUTOLINK_FILTER,
                'default'         => false,
                'extended_option' => true
            ])
        ];
    }

    public function getRules() {

        if ($this->getOption('min_length')) {
            $this->rules[] = ['min_length', $this->getOption('min_length')];
        }

        if ($this->getOption('max_length')) {
            $this->rules[] = ['max_length', $this->getOption('max_length')];
        }

        return $this->rules;
    }

    public function parseTeaser($value) {

        if (is_empty_value($value)) {
            return '';
        }

        if (!empty($this->item['is_private_item'])) {
            return '<p class="private_field_hint text-muted">' . $this->item['private_item_hint'] . '</p>';
        }

        $max_len = $this->getOption('teaser_len', 0);

        if ($max_len) {
            $value = string_short($value, $max_len);
            return $value;
        }

        return parent::parseTeaser($value);
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('is_autolink')) {
            return html_search_bar($value, href_to($this->item['ctype_name']) . '?' . $this->name . '=', 'string_autolink ' . $this->item['ctype_name'] . '_string_autolink');
        }

        return html($value, false);
    }

    public function applyFilter($model, $value) {

        switch ($this->getOption('in_filter_as')) {

            case 'select':
                return $model->filterEqual($this->name, $value);

            case 'checkbox':
                if ($value) { // работает и без этого
                    return $model->filterNotNull($this->name);
                }

            default:
                return $model->filterLike($this->name, '%' . $value . '%');
        }

        return $model;
    }

    public function getFilterInput($value) {
        if ($this->getOption('in_filter_as') === 'select') {
            $this->data['items'] = ['' => ''];
            if ($this->hasDefaultValue()) {
                $this->data['items'] += string_explode_list($this->getDefaultValue(), true);
            }
        }
        return parent::getFilterInput($value);
    }

    public function store($value, $is_submitted, $old_value = null) {

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getProperty('is_clean_disable') === true) {

            // Разрешены HTML теги, - прогоняем через типограф
            $value = cmsEventsManager::hook('html_filter', [
                'text'                => $value,
                'is_auto_br'          => false,
                'build_redirect_link' => false
            ]);

            return trim($value, " \0");
        }

        return trim(strip_tags($value), " \0");
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

    public function getStringValue($value) {
        return $value;
    }

    public function getInput($value) {

        $this->data['type']         = $this->getProperty('is_password') ? 'password' : $this->getProperty('type');
        $this->data['autocomplete'] = $this->getProperty('autocomplete');
        $this->data['attributes']   = $this->getProperty('attributes') ?: ['autocomplete' => 'off'];

        if ($this->data['autocomplete']) {
            if (empty($this->data['autocomplete']['data'])) {
                $this->data['autocomplete']['data'] = false;
            }
            if (empty($this->data['autocomplete']['url'])) {
                $this->data['autocomplete']['url'] = false;
            }
            if (empty($this->data['autocomplete']['multiple_separator'])) {
                $this->data['autocomplete']['multiple_separator'] = ', ';
            }
        }

        $this->data['attributes']['placeholder'] = $this->data['attributes']['placeholder'] ?? $this->getOption('placeholder', false);
        $this->data['attributes']['id'] = $this->id;
        $this->data['attributes']['required'] = (array_search(['required'], $this->getRules()) !== false);

        return parent::getInput($value);
    }

}
