<?php

class onCommentsCtypeBasicForm extends cmsAction {

    public function run($form) {

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_COMMENTS, 'comments', ['is_collapsed' => true]);

        $form->addField($fieldset, new fieldCheckbox('is_comments', [
            'title' => LANG_CP_COMMENTS_ON
        ]));

        $form->addField($fieldset, new fieldList('options:comments_template', [
            'title'     => LANG_CP_COMMENTS_TEMPLATE,
            'hint'      => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/comments/comment*'),
            'generator' => function ($item) {
                return $this->cms_template->getAvailableTemplatesFiles('controllers/comments', 'comment*.tpl.php');
            },
            'visible_depend' => ['is_comments' => ['show' => ['1']]]
        ]));

        $item_fields = $form->getData('item_fields');

        $item_fields['comments_spell_count'] = LANG_COMMENTS;

        $form->addField($fieldset, new fieldString('options:comments_title_pattern', [
            'title'          => LANG_CP_COMMENTS_TITLE_PATTERN,
            'multilanguage'  => true,
            'patterns_hint'  => [
                'patterns' => $item_fields
            ],
            'visible_depend' => ['is_comments' => ['show' => ['1']]]
        ]));

        foreach (array_keys((array) $this->labels) as $label_key) {
            $form->addField($fieldset, new fieldString('options:comments_labels:' . $label_key, [
                'title'            => LANG_CP_COMMENTS_REPLACE_LABEL . ' "<b>' . html($this->labels->{$label_key}, false) . '</b>"',
                'is_clean_disable' => true,
                'multilanguage'    => true,
                'visible_depend'   => ['is_comments' => ['show' => ['1']]]
            ]));
        }

        return $form;
    }

}
