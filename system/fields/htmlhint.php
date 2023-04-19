<?php

class fieldHtmlhint extends cmsFormField {

    public $is_virtual  = true;
    public $allow_index = false;

    protected $use_language = true;

	public function __construct($name, $options = false) {

        parent::__construct($name, $options);

        if(!$this->title){
            $this->title = LANG_F_HTMLHINT;
        }
    }

    public function getOptions(){
        return [
            new fieldHtml('html', [
                'title' => LANG_F_HTMLHINT_EDITOR,
                'hint' => LANG_F_HTMLHINT_EDITOR_HINT,
                'options' => ['editor' => 'ace']
            ])
        ];
    }

    public function getFilterInput($value) {
        return '';
    }

    public function parse($value){
        return '';
    }

}
