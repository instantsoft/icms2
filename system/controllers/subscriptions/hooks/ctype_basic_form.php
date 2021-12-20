<?php

class onSubscriptionsCtypeBasicForm extends cmsAction {

    public function run($form) {

        $meta_fields = [
            'subjects'        => LANG_SBSCR_SUBJECTS_URLS,
            'unsubscribe_url' => LANG_SBSCR_UNSUBSCRIBE_URL,
            'list_url'        => LANG_SBSCR_LIST_URL,
            'title'           => LANG_SBSCR_LIST_TITLE,
            'site'            => LANG_CP_SETTINGS_SITENAME,
            'date'            => LANG_DATE,
            'time'            => LANG_PARSER_CURRENT_TIME,
            'nickname'        => LANG_USER
        ];

        $fieldset = $form->addFieldsetAfter('folders', LANG_SUBSCRIPTIONS_CONTROLLER, 'subscriptions', ['is_collapsed' => true]);

        $form->addField($fieldset, new fieldCheckbox('options:enable_subscriptions', [
            'title'   => LANG_SBSCR_CTYPE_ON,
            'default' => true
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:subscriptions_recursive_categories', [
            'title'          => LANG_SBSCR_CTYPE_RECURSIVE_CATEGORIES,
            'default'        => true,
            'visible_depend' => ['options:enable_subscriptions' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldHtml('options:subscriptions_letter_tpl', [
            'title' => LANG_SBSCR_LETTER_TPL,
            'hint' => LANG_SBSCR_LETTER_TPL_HINT,
            'options' => ['editor' => 'ace'],
            'patterns_hint' => [
                'patterns' =>  $meta_fields,
                'text_panel' => '',
                'always_show' => true,
                'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
            ],
            'visible_depend' => ['options:enable_subscriptions' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldString('options:subscriptions_notify_text', [
            'title' => LANG_SBSCR_NOTIFY_TEXT,
            'hint' => LANG_SBSCR_NOTIFY_TEXT_HINT,
            'is_clean_disable' => true,
            'visible_depend' => ['options:enable_subscriptions' => ['show' => ['1']]]
        ]));

        return $form;
    }

}
