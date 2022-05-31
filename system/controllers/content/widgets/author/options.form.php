<?php

class formWidgetContentAuthorOptions extends cmsForm {

    public function init($options = false) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:show_fields', [
                        'title' => LANG_WD_CON_AUTHOR_SHOW_FIELDS,
                        'is_chosen_multiple' => true,
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $model->setTablePrefix('');
                            $model->orderBy('ordering');
                            $fields = $model->getContentFields('{users}');

                            $items = [];

                            if ($fields) {
                                foreach ($fields as $field) {
                                    $items[$field['name']] = $field['title'];
                                }
                            }

                            return $items;
                        },
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('options:show_user_items_link', [
                        'title' => LANG_WD_CON_AUTHOR_SHOW_USER_ITEMS_LINK
                    ]),
                    new fieldString('options:user_items_link_title', [
                        'title' => LANG_WD_CON_AUTHOR_USER_ITEMS_LINK_TITLE,
                        'visible_depend' => ['options:show_user_items_link' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:show_user_groups', [
                        'title' => LANG_WD_CON_AUTHOR_SHOW_USER_GROUPS
                    ]),
                    new fieldCheckbox('options:show_date_log', [
                        'title' => LANG_WD_CON_AUTHOR_SHOW_DATE_LOG
                    ]),
                    new fieldCheckbox('options:show_date_reg', [
                        'title' => LANG_WD_CON_AUTHOR_SHOW_DATE_REG
                    ]),
                    new fieldCheckbox('options:generate_schemaorg', [
                        'title' => LANG_WD_CON_GENERATE_SCHEMAORG,
                        'hint' => LANG_WD_CON_GENERATE_SCHEMAORG_HINT
                    ]),
                    new fieldHtml('options:schemaorg_addons', [
                        'title' => LANG_WD_CON_SCHEMAORG_ADDON,
                        'hint'  => LANG_WD_CON_SCHEMAORG_ADDON_HINT,
                        'options' => ['editor' => 'ace', 'editor_options' => ['mode' => 'ace/mode/json']],
                        'visible_depend' => ['options:generate_schemaorg' => ['show' => ['1']]]
                    ])
                ]
            ]
        ];
    }

}
