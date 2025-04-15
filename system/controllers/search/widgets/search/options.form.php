<?php

class formWidgetSearchSearchOptions extends cmsForm {

    public function init($options = false) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:show_input', [
                        'title' => LANG_WD_SEARCH_SHOW_INPUT,
                        'default' => 1
                    ]),
                    new fieldCheckbox('options:show_btn', [
                        'title' => LANG_WD_SEARCH_SHOW_BTN
                    ]),
                    new fieldCheckbox('options:show_search_params', [
                        'title' => LANG_SEARCH_SHOW_PARAMS
                    ])
                ]
            ]
        ];
    }

}
