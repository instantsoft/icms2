<?php

class fieldColor extends cmsFormField {

    public $title       = LANG_PARSER_COLOR;
    public $sql         = 'varchar(7) NULL DEFAULT NULL';
    public $filter_hint = '#RRGGBB';
    public $filter_type = 'str';
    public $var_type    = 'string';

    public function getOptions(){
        return array(
            new fieldList('control_type', array(
                'title'   => LANG_PARSER_COLOR_CT,
                'default' => 'hue',
                'items'   => array(
                    'hue'        => LANG_PARSER_COLOR_CT_HUE,
                    'saturation' => LANG_PARSER_COLOR_CT_SATURATION,
                    'brightness' => LANG_PARSER_COLOR_CT_BRIGHTNESS,
                    'wheel'      => LANG_PARSER_COLOR_CT_WHEEL,
                    'swatches'   => LANG_PARSER_COLOR_CT_SWATCHES
                )
            )),
            new fieldString('swatches', array(
                'title'   => LANG_PARSER_COLOR_CT_SWATCHES_OPT,
                'default' => '#fff, #000, #f00, #0f0, #00f, #ff0, #0ff'
            ))
        );
    }

    public function getRules() {

        $this->rules[] = array('color');

        return $this->rules;

    }

    public function parse($value){
        return '<div class="color-block" style="background-color:'.$value.'" title="'.$value.'"></div>';
    }

    public function getStringValue($value){
        return $value;
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, $value);
    }

    public function getInput($value) {

        $_swatches = $this->getOption('swatches');

        if($_swatches){

            $swatches = explode(',', $_swatches);

            foreach($swatches as $id => $rgb){
                $swatches[$id] = trim($rgb);
            }

        } else {
            $swatches = array();
        }

        $this->setOption('swatches', $swatches);

        return parent::getInput($value);

    }

}
