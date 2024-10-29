<?php

class fieldChild extends cmsFormField {

    public $title                = LANG_PARSER_CHILD;
    public $is_public            = false;
    public $sql                  = '';
    public $is_virtual           = true;
    public $allow_index          = false;
    public $excluded_controllers = ['forms', 'users', 'groups'];
    public $filter_type          = 'int';
    public $var_type             = 'integer';

    public function getStringValue($value) {
        return '';
    }

    public function getInput($value) {
        return '';
    }

    public function parseTeaser($value) {
        return '';
    }

    public function parse($list) {

        $html = '';

        if (!$list) {
            return $html;
        }

        if ($list['title']){
            $html .= '<h2>'.$list['title'].'</h2>';
        }

        $html .= $list['html'];

        return $html;
    }

}
