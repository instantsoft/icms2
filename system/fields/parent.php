<?php

class fieldParent extends cmsFormField {

    public $title       = LANG_PARSER_PARENT;
    public $is_public   = false;
    public $sql         = 'int NULL DEFAULT NULL';
    public $allow_index = true;
    public $var_type    = 'int';

    public function parse($value){

        if ($value){
            $parent_item = $this->getParentItem($value);
        }

        if (!$value || !$parent_item){
            return '';
        }

        $parent_url = href_to($parent_item['ctype_name'], $parent_item['slug'].'.html');
        return '<a href="'.$parent_url.'">'.$parent_item['title'].'</a>';

    }

    public function getInput($value) {

        $parent_item = false;

        if ($value){
            $parent_item = $this->getParentItem($value);
        }

        $this->title = $this->element_title;

        return cmsTemplate::getInstance()->renderFormField($this->class, array(
            'field' => $this,
            'value' => $value,
            'item' => $parent_item
        ));

    }

    private function getParentItem($value){

        preg_match('/parent_([a-z0-9\-\_]+)_id/i', $this->name, $matches);

        if (!$matches || empty($matches[1])){ return false; }

        $ctype_name = $matches[1];

        $content_model = cmsCore::getModel('content');

        $parent_item = $content_model->getContentItem($ctype_name, $value);

        if (!$parent_item) { return false; }

        $parent_item['ctype_name'] = $ctype_name;

        return $parent_item;

    }

}