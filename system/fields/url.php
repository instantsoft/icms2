<?php

class fieldUrl extends cmsFormField {

    public $title       = LANG_PARSER_URL;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';

    public function getOptions(){
        return array(
            new fieldCheckbox('redirect', array(
                'title' => LANG_PARSER_URL_REDIRECT,
                'default' => false,
                'is_visible' => cmsController::enabled('redirect')
            )),
            new fieldCheckbox('auto_http', array(
                'title' => LANG_PARSER_URL_AUTO_HTTP,
                'default' => true
            )),
            new fieldNumber('max_length', array(
                'title' => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 500
            )),
            new fieldCheckbox('nofollow', array(
                'title'   => LANG_PARSER_URL_NOFOLLOW,
                'default' => false
            )),
            new fieldCheckbox('title', array(
                'title'   => LANG_PARSER_URL_TITLE,
                'default' => false
            )),
            new fieldString('css_class', array(
                'title'   => LANG_PARSER_URL_CSS_CLASS,
                'rules'   => array(
                    array('max_length', 50)
                )
            ))
        );
    }

    public function getStringValue($value){

        if (!$value) {
            return '';
        }

        if ($this->getOption('title') && strpos( $value, '|') !== false){

            $result = explode('|', $value);

            if(!empty($result[1])){
                $value = trim($result[1]);
            }
        }

        return $value;
    }

    public function parse($value) {

        if (!$value) {
            return '';
        }

        if (!$this->getOption('title') && strpos($value, '|') === false) {

            $href = $value;

        } else {

            $result = explode('|', $value);

            $href = trim($result[0]);

            if (!empty($result[1])) {
                $value = trim($result[1]);
            }
        }

        if ($this->getOption('auto_http')) {
            if (!preg_match('/^([a-z]+):\/\/(.+)$/i', $href)) {
                $href = 'http://' . $href;
            }
        }

        if ($this->getOption('redirect') && cmsController::enabled('redirect')) {
            $href = href_to('redirect') . '?url=' . urlencode($href);
        }

        $nofollow = $class    = '';

        if ($this->getOption('nofollow')) {
            $nofollow = ' nofollow';
        }

        if ($this->getOption('css_class')) {
            $class = ' class="' . $this->getOption('css_class') . '"';
        }

        return '<a rel="noopener' . $nofollow . '" target="_blank" ' . $class . ' href="' . html($href, false) . '">' . html($value, false) . '</a>';
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value = null) {
        return strip_tags($value);
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

}
