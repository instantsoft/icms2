<?php

class formCspOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('enable_csp', [
                        'title' => LANG_CSP_ENABLE_CSP,
                        'hint' => LANG_CSP_ENABLE_CSP_HINT,
                        'default' => 0
                    ]),
                    new fieldCheckbox('enable_report', [
                        'title' => LANG_CSP_ENABLE_REPORT,
                        'default' => 1
                    ]),
                    new fieldCheckbox('is_report_only', [
                        'title' => LANG_CSP_IS_REPORT_ONLY,
                        'hint'  => LANG_CSP_IS_REPORT_ONLY_HINT,
                        'default' => 1
                    ]),
                    new fieldString('csp_str', [
                        'title' => LANG_CSP_CSP_STR,
                        'hint' => LANG_CSP_CSP_STR_HINT,
                        'default' => '',
                        'options' => [
                            'max_length' => 1000
                        ],
                        'attributes' => [
                            'readonly' => true,
                            'v-model' => 'cspString'
                        ]
                    ])
                ]
            ]
        ];
    }

}
