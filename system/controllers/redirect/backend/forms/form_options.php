<?php

class formRedirectOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldText('no_redirect_list', [
                        'title' => LANG_REDIRECT_NO_REDIRECT_LIST,
                        'hint'  => LANG_REDIRECT_ADMIN_HINT . LANG_REDIRECT_NO_REDIRECT_LIST_HINT
                    ]),
                    new fieldText('black_list', [
                        'title' => LANG_REDIRECT_BLACK_LIST,
                        'hint'  => LANG_REDIRECT_ADMIN_HINT . LANG_REDIRECT_BLACK_LIST_HINT
                    ]),
                    new fieldCheckbox('is_check_link', [
                        'title'   => LANG_REDIRECT_IS_CHECK_LINK,
                        'hint'    => LANG_REDIRECT_IS_CHECK_LINK_HINT,
                        'default' => 1
                    ]),
                    new fieldString('vk_access_token', [
                        'title'   => LANG_REDIRECT_VK_ACCESS_TOKEN,
                        'hint'    => LANG_REDIRECT_VK_ACCESS_TOKEN_HINT,
                        'visible_depend' => ['is_check_link' => ['show' => ['1']]]
                     ]),
                    new fieldText('white_list', [
                        'title' => LANG_REDIRECT_WHITE_LIST,
                        'hint'  => LANG_REDIRECT_ADMIN_HINT . LANG_REDIRECT_WHITE_LIST_HINT
                    ]),
                    new fieldNumber('redirect_time', [
                        'title'   => LANG_REDIRECT_REDIRECT_TIME,
                        'units'   => LANG_SECONDS,
                        'default' => 10
                    ]),
                    new fieldCheckbox('is_check_refer', [
                        'title' => LANG_REDIRECT_IS_CHECK_REFER
                    ]),
                    new fieldHtml('rewrite_json', [
                        'title'   => LANG_REDIRECT_REWRITE_JSON,
                        'hint'    => nl2br(LANG_REDIRECT_REWRITE_JSON_HINT),
                        'options' => ['editor' => 'ace', 'editor_options' => ['mode' => 'ace/mode/json']],
                    ])
                ]
            ]
        ];
    }

}
