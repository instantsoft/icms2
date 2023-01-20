<?php

class formWidgetHtmlOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldHtml('options:content', [
                        'title'   => LANG_WD_HTML_CONTENT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'unset_required' => true
                        ],
                        'options' => ['editor' => 'ace'],
                        'rules'   => [
                            ['required']
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => 'JavaScript/CSS',
                'childs' => [
                    new fieldText('options:css_files', [
                        'title' => LANG_WD_HTML_CSS_FILES,
                        'hint' => LANG_WD_HTML_CSS_FILES_HINT,
                        'is_strip_tags' => true
                    ]),
                    new fieldText('options:js_files', [
                        'title' => LANG_WD_HTML_JS_FILES,
                        'hint' => LANG_WD_HTML_JS_FILES_HINT,
                        'is_strip_tags' => true
                    ]),
                    new fieldHtml('options:js_inline_scripts', [
                        'title' => LANG_WD_HTML_JS_INLINE_SCRIPTS,
                        'hint' => LANG_WD_HTML_JS_INLINE_SCRIPTS_HINT,
                        'options' => ['editor' => 'ace', 'editor_options' => ['mode' => 'ace/mode/javascript']],
                    ])
                ]
            ],
        ];
    }

}
