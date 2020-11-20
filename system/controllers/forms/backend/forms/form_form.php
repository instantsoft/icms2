<?php
class formFormsForm extends cmsForm {

    public $is_tabbed = true;

    public function init($do, $form_data = [], $fields = []) {

        $meta_fields = [
            'form_title' => LANG_FORMS_CP_TITLE,
            'form_data'  => LANG_FORMS_CP_META_DATA,
            'page_url'   => LANG_FORMS_PAGE_URL,
            'user_name'  => LANG_USER,
            'ip'         => 'IP'
        ];

        if($fields){
            foreach ($fields as $field) {
                $meta_fields[$field['name']] = $field['title'];
            }
        }

        return array(

            'basic' => array(
                'title' => LANG_CP_BASIC,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'hint' => LANG_FORMS_CP_NAME_HINT,
                        'options'=>array(
                            'max_length' => 32,
                            'show_symbol_count' => true
                        ),
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            (in_array($do, ['add', 'copy']) ? array('unique', 'forms', 'name') : array('unique_exclude', 'forms', 'name', $form_data['id']))
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_FORMS_CP_TITLE,
                        'options'=>array(
                            'max_length' => 255,
                            'show_symbol_count' => true
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldHtml('description', array(
                        'title' => LANG_DESCRIPTION
                    )),
                    new fieldList('tpl_form', array(
                        'title' => LANG_FORMS_CP_TPL_FORM,
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'form*.tpl.php', false, ['form_fields']);
                       }
                    ))
                )
            ),
            'options' => array(
                'title' => LANG_OPTIONS,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldCheckbox('options:show_title', array(
                        'title' => LANG_SHOW_TITLE
                    )),
                    new fieldCheckbox('options:available_by_link', array(
                        'title' => LANG_FORMS_CP_AVAILABLE_BY_LINK
                    )),
                    new fieldCheckbox('options:hide_after_submit', array(
                        'title' => LANG_FORMS_CP_HIDE_AFTER_SUBMIT
                    )),
                    new fieldList('options:send_type', array(
                        'title' => LANG_FORMS_CP_SEND_TYPE,
                        'hint' => LANG_FORMS_CP_SEND_TYPE_HINT,
                        'is_chosen_multiple' => true,
                        'items' => [
                            'notice' => LANG_FORMS_CP_SEND_TYPE1,
                            'email'  => LANG_FORMS_CP_SEND_TYPE2,
                            'author' => LANG_FORMS_CP_SEND_TYPE3
                        ]
                    )),
                    new fieldString('options:send_type_notice', array(
                        'title' => LANG_USERS,
                        'hint' => LANG_FORMS_CP_SEND_USERS_HINT,
                        'autocomplete' => array('url' => href_to('admin', 'users', 'autocomplete'), 'multiple' => true),
                        'visible_depend' => array('options:send_type:' => array('show' => array('notice')))
                    )),
                    new fieldHtml('options:notify_text', array(
                        'title' => LANG_FORMS_CP_FORM_NOTIFY_TEXT,
                        'hint' => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'visible_depend' => array('options:send_type:' => array('show' => array('notice'))),
                        'patterns_hint' => [
                            'patterns' =>  $meta_fields,
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    )),
                    new fieldString('options:send_type_email', array(
                        'title' => LANG_EMAIL,
                        'hint' => LANG_FORMS_CP_SEND_EMAIL_HINT,
                        'visible_depend' => array('options:send_type:' => array('show' => array('email')))
                    )),
                    new fieldHtml('options:letter', array(
                        'title' => LANG_FORMS_CP_FORM_LETTER,
                        'hint' => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'options' => [
                            'editor' => 'ace'
                        ],
                        'visible_depend' => array('options:send_type:' => array('show' => array('email'))),
                        'patterns_hint' => [
                            'patterns' =>  $meta_fields,
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    )),
                    new fieldString('options:action', array(
                        'title' => LANG_FORMS_CP_ACTION,
                        'hint' => LANG_FORMS_CP_ACTION_HINT
                    )),
                    new fieldList('options:method', array(
                        'title' => LANG_FORMS_CP_METHOD,
                        'hint' => LANG_FORMS_CP_METHOD_HINT,
                        'items' => [
                            'ajax' => 'POST ajax',
                            'post' => 'POST',
                            'get'  => 'GET'
                        ],
                        'visible_depend' => array('options:action' => array('hide' => array('')))
                    )),
                    new fieldText('options:send_text', array(
                        'title' => LANG_FORMS_CP_SEND_TEXT_FORM,
                        'hint' => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'patterns_hint' => [
                            'patterns' => $meta_fields,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    )),
                    new fieldString('options:continue_link', array(
                        'title' => LANG_FORMS_CP_CONTINUE_LINK
                    ))
                )
            )

        );

    }

}
