<?php

class formModerationOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('moderation_log_delete', [
                        'title' => LANG_MODERATION_LOG_DELETE
                    ]),
                    new fieldCheckbox('moderation_log_restore', [
                        'title' => LANG_MODERATION_LOG_RESTORE
                    ]),
                    new fieldCheckbox('clear_log_after_delete', [
                        'title' => LANG_MODERATION_CLEAR_LOG_AFTER_DELETE
                    ])
                ]
            ]
        ];
    }

}
