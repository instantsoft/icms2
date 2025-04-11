<?php

class formWidgetSearchSearchOptions extends cmsForm {

    public function init($options = false) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:show_btn', [
                        'title' => LANG_WD_SEARCH_SHOW_BTN
                    ])
                ]
            ]
        ];
    }

}
