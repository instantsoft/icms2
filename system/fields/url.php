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
                'title'   => LANG_PARSER_URL_REDIRECT,
                'default' => false
            )),
            new fieldCheckbox('auto_http', array(
                'title'   => LANG_PARSER_URL_AUTO_HTTP,
                'default' => true
            )),
            new fieldCheckbox('target', array(
                'title'   => LANG_PARSER_URL_TARGET,
                'default' => false
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
            )),
            new fieldNumber('max_length', array(
                'title'   => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 500
            )) 
        );
    }

    public function parse($value){

        $result = strpos('|', trim($value));
        
        if ($result === false && !$this->getOption('title')){
            $href = $value; 
        }else{
            $result = explode('|', $value);
            $href = trim($result[0]); 
        }
 
        if ($this->getOption('auto_http')){
            if (!preg_match('/^([a-z]+):\/\/(.+)$/i', $href)) { $href = 'http://' . $href; }
        }

        if ($this->getOption('redirect')){
            $href = cmsConfig::get('root') . 'redirect?url=' . $href;
        }
        
        $this->getOption('target')    ? $target = 'target="_blank"'  : '';
        $this->getOption('nofollow')  ? $nofollow = 'rel="nofollow"' : '';
        $this->getOption('css_class') ? $class = "class=".$this->options['css_class']  : '';
        !empty($result[1]) ? $value = trim($result[1]) : $value = $href;

        return '<a href="'.htmlspecialchars($href).'" '.$class.' '.$target.' '.$nofollow.'>'.htmlspecialchars($value).'</a>';

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value=null){
        return strip_tags($value);
    }

}
