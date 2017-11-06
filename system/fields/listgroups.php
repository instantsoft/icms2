<?php

class fieldListGroups extends cmsFormField {

    public $title       = LANG_PARSER_LIST_GROUPS;
    public $is_public   = false;
    public $sql         = 'text NULL DEFAULT NULL';
    public $allow_index = false;
    public $var_type    = 'array';

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

    public function getListItems(){

        $users_model = cmsCore::getModel('users');

        $items = $this->getProperty('show_all') ? array(0 => LANG_ALL) : array();

        $groups = $users_model->getGroups((bool)$this->getProperty('show_guests'));

        foreach($groups as $group){
            $items[$group['id']] = $group['title'];
        }

        return $items;

    }

    public function getInput($value){


        $this->data['groups'] = $this->getListItems();

        if(!is_array($value)){
            $value = cmsModel::yamlToArray($value);
        }

        return parent::getInput($value ? $value : array(0));

    }

}
