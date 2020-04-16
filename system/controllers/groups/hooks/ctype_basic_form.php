<?php

class onGroupsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_CT_GROUPS, 'groups', array('is_collapsed' => true));

        $form->addField($fieldset,new fieldCheckbox('is_in_groups', array(
            'title' => LANG_CP_CT_GROUPS_ALLOW
        )));

        $form->addField($fieldset,new fieldCheckbox('is_in_groups_only', array(
            'title' => LANG_CP_CT_GROUPS_ALLOW_ONLY,
            'visible_depend' => array('is_in_groups' => array('show' => array('1')))
        )));

        return $form;

    }

}
