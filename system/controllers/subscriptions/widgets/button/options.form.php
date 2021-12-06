<?php

class formWidgetSubscriptionsButtonOptions extends cmsForm {

    public function init($options = false) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:hide_all', [
                        'title' => LANG_SBSCR_WD_HIDE_ALL
                    ]),
                    new fieldCheckbox('options:hide_all_title', [
                        'title' => LANG_SBSCR_WD_HIDE_TITLE,
                        'visible_depend' => ['options:hide_all' => ['show' => ['0']]]
                    ]),
                    new fieldCheckbox('options:hide_user', [
                        'title' => LANG_SBSCR_WD_HIDE_USER
                    ]),
                    new fieldCheckbox('options:hide_user_title', [
                        'title' => LANG_SBSCR_WD_HIDE_TITLE,
                        'visible_depend' => ['options:hide_user' => ['show' => ['0']]]
                    ]),
                    new fieldCheckbox('options:hide_cat', [
                        'title' => LANG_SBSCR_WD_HIDE_CAT
                    ]),
                    new fieldCheckbox('options:hide_cat_title', [
                        'title' => LANG_SBSCR_WD_HIDE_TITLE,
                        'visible_depend' => ['options:hide_cat' => ['show' => ['0']]]
                    ]),
                    new fieldCheckbox('options:hide_album', [
                        'title' => LANG_SBSCR_WD_HIDE_ALBUM
                    ]),
                    new fieldCheckbox('options:hide_album_title', [
                        'title' => LANG_SBSCR_WD_HIDE_TITLE,
                        'visible_depend' => ['options:hide_album' => ['show' => ['0']]]
                    ])
                ]
            ]
        ];
    }

}
