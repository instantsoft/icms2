<?php

class fieldCaption extends cmsFormField {

    public $title       = LANG_PARSER_CAPTION;
    public $is_public   = false;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $allow_index = true;
    public $var_type    = 'string';

    protected $is_set_rules = false;

    public function getOptions() {

        return [
            new fieldNumber('min_length', [
                'title'   => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            ]),
            new fieldNumber('max_length', [
                'title'   => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 255
            ]),
            new fieldString('placeholder', [
                'title' => LANG_PARSER_PLACEHOLDER,
                'can_multilanguage' => true
            ]),
            new fieldCheckbox('show_symbol_count', [
                'title' => LANG_PARSER_SHOW_SYMBOL_COUNT
            ]),
            new fieldCheckbox('in_fulltext_search', [
                'title'   => LANG_PARSER_IN_FULLTEXT_SEARCH,
                'default' => true
            ])
        ];
    }

    public function getRules() {

        if (!$this->is_set_rules) {

            if ($this->getOption('min_length')) {
                if (array_search(['required'], $this->rules) === false) {
                    $this->rules[] = ['required'];
                }
                $this->rules[] = ['min_length', $this->getOption('min_length')];
            }

            if ($this->getOption('max_length')) {
                $this->rules[] = ['max_length', $this->getOption('max_length')];
            }

            $this->is_set_rules = true;
        }

        return $this->rules;
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        return '<h1>' . html($value, false) . '</h1>';
    }

    public function getStringValue($value) {
        return $value ? $value : '';
    }

    public function store($value, $is_submitted, $old_value = null) {
        return $value ? strip_tags($value) : null;
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function getInput($value) {

        $this->data['attributes'] = $this->getProperty('attributes') ?: ['autocomplete' => 'off'];
        $this->data['attributes']['placeholder'] = $this->data['attributes']['placeholder'] ?? $this->getOption('placeholder', false);
        $this->data['attributes']['id'] = $this->id;
        $this->data['attributes']['required'] = (array_search(['required'], $this->getRules()) !== false);

        return parent::getInput($value);
    }

}
