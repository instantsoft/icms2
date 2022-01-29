<?php

class fieldColor extends cmsFormField {

    public $title       = LANG_PARSER_COLOR;
    public $sql         = 'varchar(25) NULL DEFAULT NULL';
    public $filter_hint = '#RRGGBB';
    public $filter_type = 'str';
    public $var_type    = 'string';

    public function getOptions() {
        return [
            new fieldList('control_type', [
                'title'   => LANG_PARSER_COLOR_CT,
                'default' => 'hue',
                'items'   => [
                    'hue'        => LANG_PARSER_COLOR_CT_HUE,
                    'saturation' => LANG_PARSER_COLOR_CT_SATURATION,
                    'brightness' => LANG_PARSER_COLOR_CT_BRIGHTNESS,
                    'wheel'      => LANG_PARSER_COLOR_CT_WHEEL,
                    'swatches'   => LANG_PARSER_COLOR_CT_SWATCHES
                ]
            ]),
            new fieldCheckbox('opacity', [
                'title'   => LANG_PARSER_COLOR_OPACITY,
                'default' => false
            ]),
            new fieldString('swatches', [
                'title'   => LANG_PARSER_COLOR_CT_SWATCHES_OPT,
                'default' => '#fff, #000, #f00, #0f0, #00f, #ff0, #0ff'
            ])
        ];
    }

    public function getRules() {

        $this->rules[] = ['color'];

        return $this->rules;
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        return '<div class="color-block" style="background-color:' . $value . '" title="' . html($value, false) . '"></div>';
    }

    public function getStringValue($value) {
        return $value ? $value : '';
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, $value);
    }

    public function getInput($value) {

        $_swatches = $this->getOption('swatches');

        if ($_swatches) {

            $swatches = explode(',', $_swatches);

            foreach ($swatches as $id => $rgb) {
                $swatches[$id] = trim($rgb);
            }
        } else {
            $swatches = [];
        }

        $this->data['minicolors_options'] = [
            'swatches' => $swatches,
            'control'  => $this->getOption('control_type', 'hue')
        ];

        if ($this->getOption('opacity')) {
            $this->data['minicolors_options']['format']  = 'rgb';
            $this->data['minicolors_options']['opacity'] = true;
        }

        return parent::getInput($value);
    }

}
