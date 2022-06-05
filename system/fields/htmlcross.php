<?php

class fieldHtmlcross extends cmsFormField {

    public $sql                  = '';
    public $is_virtual           = true;
    public $allow_index          = false;
    public $excluded_controllers = ['forms'];

    protected $use_language = true;

	public function __construct($name, $options = false) {

        parent::__construct($name, $options);

        if(!$this->title){
            $this->title = LANG_F_HTMLCROSS;
        }
    }

    public function getOptions(){
        return [
            new fieldHtml('item_html', [
                'title'           => LANG_F_HTMLCROSS_ITEM,
                'extended_option' => true
            ]),
            new fieldCheckbox('list_as_item', [
                'title'           => LANG_F_HTMLCROSS_LIST_AS_ITEM,
                'default'         => true,
                'extended_option' => true
            ]),
            new fieldHtml('list_html', [
                'title'           => LANG_F_HTMLCROSS_LIST,
                'extended_option' => true,
                'visible_depend'  => ['options:list_as_item' => ['show' => ['0']]]
            ])
        ];
    }

    public function getInput($value){
        return '';
    }

    public function getFilterInput($value) {
        return '';
    }

    public function getStringValue($value){
        return '';
    }

    public function parse($value){

        return string_replace_svg_icons($this->getOption('item_html'));
    }

    public function parseTeaser($value) {

        $text = string_replace_svg_icons($this->getOption('list_as_item') ? $this->getOption('item_html') : $this->getOption('list_html'));

        if (!empty($this->item)){
            $text = string_replace_keys_values_extended($text, $this->item);
        }

        return $text;
    }

    public function afterParse($value, $item){
        return string_replace_keys_values_extended($this->getOption('item_html'), $item);
    }

}
