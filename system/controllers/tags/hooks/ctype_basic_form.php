<?php

class onTagsCtypeBasicForm extends cmsAction {

    public function run($form) {

        $fieldset = $form->addFieldsetAfter('folders', LANG_TAGS, 'tags', ['is_collapsed' => true]);

        $form->addField($fieldset, new fieldCheckbox('is_tags', [
            'title' => LANG_CP_TAGS_ON
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:is_tags_in_list', [
            'title'          => LANG_CP_TAGS_IN_LIST,
            'visible_depend' => ['is_tags' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:is_tags_in_item', [
            'title'          => LANG_CP_TAGS_IN_ITEM,
            'visible_depend' => ['is_tags' => ['show' => ['1']]]
        ]));

        return $form;
    }

}
