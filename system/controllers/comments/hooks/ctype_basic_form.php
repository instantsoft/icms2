<?php

class onCommentsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_COMMENTS, 'comments', array('is_collapsed' => true));

        $form->addField($fieldset, new fieldCheckbox('is_comments', array(
            'title' => LANG_CP_COMMENTS_ON
        )));

        foreach (array_keys((array)$this->labels) as $label_key) {
            $form->addField($fieldset, new fieldString('options:comments_labels:'.$label_key, array(
                'title' => LANG_CP_COMMENTS_REPLACE_LABEL.' "<b>'.html($this->labels->{$label_key}, false).'</b>"',
                'multilanguage' => true,
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));
        }

        return $form;

    }

}
