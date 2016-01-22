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

    public function getInput($value){

        $users_model = cmsCore::getModel('users');

        $items = $this->getProperty('show_all') ? array(0 => LANG_ALL) : array();
        $is_show_guests = (bool)$this->getProperty('show_guests');

        $groups = $users_model->getGroups($is_show_guests);

        foreach($groups as $group){
            $items[$group['id']] = $group['title'];
        }

        $this->data['groups'] = $items;

        return parent::getInput($value ? $value : array(0));

    }

}
