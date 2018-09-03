<?php

class onTagsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_TAGS, 'tags', array('is_collapsed' => true));

        $form->addField($fieldset,new fieldCheckbox('is_tags', array(
            'title' => LANG_CP_TAGS_ON
        )));

        $form->addField($fieldset, new fieldCheckbox('options:is_tags_in_list', array(
            'title' => LANG_CP_TAGS_IN_LIST,
            'visible_depend' => array('is_tags' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldCheckbox('options:is_tags_in_item', array(
            'title' => LANG_CP_TAGS_IN_ITEM,
            'visible_depend' => array('is_tags' => array('show' => array('1')))
        )));

        return $form;

    }

}
