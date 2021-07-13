<?php

class formRedirectOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type'   => 'fieldset',
                'childs' => array(

                    new fieldText('no_redirect_list', array(
                        'title' => LANG_REDIRECT_NO_REDIRECT_LIST,
                        'hint' => LANG_REDIRECT_ADMIN_HINT.LANG_REDIRECT_NO_REDIRECT_LIST_HINT
                    )),

                    new fieldText('black_list', array(
                        'title' => LANG_REDIRECT_BLACK_LIST,
                        'hint'  => LANG_REDIRECT_ADMIN_HINT.LANG_REDIRECT_BLACK_LIST_HINT
                    )),

                    new fieldCheckbox('is_check_link', array(
                        'title' => LANG_REDIRECT_IS_CHECK_LINK,
                        'hint'  => LANG_REDIRECT_IS_CHECK_LINK_HINT,
                        'default' => 1
                    )),

                    new fieldText('white_list', array(
                        'title' => LANG_REDIRECT_WHITE_LIST,
                        'hint'  => LANG_REDIRECT_ADMIN_HINT.LANG_REDIRECT_WHITE_LIST_HINT
                    )),

                    new fieldNumber('redirect_time', array(
                        'title'   => LANG_REDIRECT_REDIRECT_TIME,
                        'units'   => LANG_SECONDS,
                        'default' => 10
                    )),

                    new fieldCheckbox('is_check_refer', array(
                        'title' => LANG_REDIRECT_IS_CHECK_REFER
                    )),

                    new fieldHtml('rewrite_json', [
                        'title' => LANG_REDIRECT_REWRITE_JSON,
                        'hint' => nl2br(LANG_REDIRECT_REWRITE_JSON_HINT),
                        'options' => ['editor' => 'ace', 'editor_options' => ['mode' => 'ace/mode/json']],
                    ])

                )
            )

        );

    }

}
