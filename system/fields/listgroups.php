<?php

class fieldListGroups extends cmsFormField {

    public $title = LANG_PARSER_LIST_GROUPS;
    public $is_public = false;
    public $sql   = 'text NULL DEFAULT NULL';
	public $allow_index = false;

    public function getOptions(){
        return array(
            new fieldCheckbox('show_all', array(
                'title' => LANG_PARSER_LIST_MULTIPLE_SHOW_ALL,
                'default' => 1
            )),
            new fieldCheckbox('show_guests', array(
                'title' => LANG_PARSER_LIST_GROUPS_SHOW_GUESTS,
                'default' => 0
            )),
        );
    }

}
