<?php
class formUsersLock extends cmsForm {

    public function init() {

        return [
            'locked' => [
                'type' => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_locked', [
                        'title' => LANG_CP_USER_IS_LOCKED
                    ]),
                    new fieldDate('lock_until', [
                        'title' => LANG_CP_USER_LOCK_UNTIL
                    ]),
                    new fieldString('lock_reason', [
                        'title' => LANG_CP_USER_LOCK_REASON,
                        'rules' => [
                            ['max_length', 250]
                        ]
                    ])
                ]
            ]
        ];
    }

}
