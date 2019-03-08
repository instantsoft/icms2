<?php

class onRatingCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_RATING, 'ratings', array('is_collapsed' => true));

        $form->addField($fieldset,new fieldCheckbox('is_rating', array(
            'title' => LANG_CP_RATING_ON
        )));

        return $form;

    }

}
