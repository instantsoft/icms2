<?php

class formFormsOptions extends cmsForm {

    public function init() {

        $meta_fields_email = [
            'form_title' => LANG_FORMS_CP_TITLE,
            'form_data'  => LANG_FORMS_CP_META_DATA,
            'site'       => LANG_CP_SETTINGS_SITENAME,
            'date'       => LANG_DATE,
            'time'       => LANG_PARSER_CURRENT_TIME,
            'ip'         => 'IP'
        ];

        $meta_fields = [
            'form_title' => LANG_FORMS_CP_TITLE,
            'form_data'  => LANG_FORMS_CP_META_DATA,
            'ip'         => 'IP'
        ];

        return array(

            array(
                'type' => 'fieldset',
                'childs' => [

                    new fieldCheckbox('allow_shortcode', array(
                        'title' => LANG_FORMS_CP_ALLOW_SHORTCODE,
                    )),

                    new fieldCheckbox('allow_embed', array(
                        'title' => LANG_FORMS_CP_ALLOW_EMBED
                    )),

                    new fieldString('allow_embed_domain', array(
                        'title' => LANG_FORMS_CP_ALLOW_EMBED_DOMAIN,
                        'hint' => LANG_FORMS_CP_ENTER_DOMAIN.LANG_FORMS_CP_ALLOW_EMBED_DOMAIN_HINT,
                        'visible_depend' => array('allow_embed' => array('show' => array('1')))
                    )),

                    new fieldString('denied_embed_domain', array(
                        'title' => LANG_FORMS_CP_DENIED_EMBED_DOMAIN,
                        'hint'  => LANG_FORMS_CP_ENTER_DOMAIN.LANG_FORMS_CP_DENIED_EMBED_DOMAIN_HINT,
                        'visible_depend' => array('allow_embed' => array('show' => array('1')))
                    )),

                    new fieldText('send_text', array(
                        'title' => LANG_FORMS_CP_SEND_TEXT,
                        'patterns_hint' => [
                            'patterns' =>  $meta_fields,
                            'text_panel' => '',
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    )),

                    new fieldHtml('letter', array(
                        'title' => LANG_FORMS_CP_LETTER,
                        'hint' => LANG_FORMS_CP_LETTER_HINT,
                        'options' => [
                            'editor' => 'ace'
                        ],
                        'patterns_hint' => [
                            'patterns' =>  $meta_fields_email,
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    )),

                    new fieldHtml('notify_text', array(
                        'title' => LANG_FORMS_CP_NOTIFY_TEXT,
                        'hint' => LANG_FORMS_CP_NOTIFY_TEXT_HINT,
                        'patterns_hint' => [
                            'patterns' =>  $meta_fields,
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ))
                ]
            )

        );

    }

}
