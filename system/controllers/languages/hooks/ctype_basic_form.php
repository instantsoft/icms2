<?php

class onLanguagesCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_LANGUAGES_CONTROLLER, 'languages', ['is_collapsed' => true]);

        $form->addField($fieldset,new fieldCheckbox('options:is_multilanguages', [
            'title' => LANG_LANGUAGES_CP_CTYPE,
            'hint'  => LANG_LANGUAGES_CP_CTYPE_HINT
        ]));

        return $form;
    }

}
