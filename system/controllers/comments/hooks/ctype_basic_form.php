<?php

class onCommentsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_COMMENTS, 'comments', array('is_collapsed' => true));

        $form->addField($fieldset,new fieldCheckbox('is_comments', array(
            'title' => LANG_CP_COMMENTS_ON
        )));

        return $form;

    }

}
