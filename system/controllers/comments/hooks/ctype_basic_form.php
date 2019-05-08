<?php

class onCommentsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_COMMENTS, 'comments', array('is_collapsed' => true));

        $form->addField($fieldset, new fieldCheckbox('is_comments', array(
            'title' => LANG_CP_COMMENTS_ON
        )));

        $form->addField($fieldset, new fieldList('options:comments_template', array(
            'title' => LANG_CP_COMMENTS_TEMPLATE,
            'hint'  => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/comments/comment*'),
            'generator' => function($item) {
                return $this->cms_template->getAvailableTemplatesFiles('controllers/comments', 'comment*.tpl.php');
            },
            'visible_depend' => array('is_comments' => array('show' => array('1')))
        )));

        $item_fields = $form->getData('item_fields');

        $form->addField($fieldset, new fieldString('options:comments_title_pattern', array(
            'title' => LANG_CP_COMMENTS_TITLE_PATTERN,
            'multilanguage' => true,
            'patterns_hint' => [
                'patterns' =>  $item_fields
            ],
            'visible_depend' => array('is_comments' => array('show' => array('1')))
        )));

        foreach (array_keys((array)$this->labels) as $label_key) {
            $form->addField($fieldset, new fieldString('options:comments_labels:'.$label_key, array(
                'title' => LANG_CP_COMMENTS_REPLACE_LABEL.' "<b>'.html($this->labels->{$label_key}, false).'</b>"',
                'is_clean_disable' => true,
                'multilanguage' => true,
                'visible_depend' => array('is_comments' => array('show' => array('1')))
            )));
        }

        return $form;

    }

}
