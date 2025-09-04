<?php
class formAdminSchedulerTask extends cmsForm {

    public function init() {

        return [
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_DESCRIPTION,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('controller', [
                        'title' => LANG_CP_SCHEDULER_TASK_CONTROLLER,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('hook', [
                        'title' => LANG_CP_SCHEDULER_TASK_HOOK,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldNumber('period', [
                        'title' => LANG_CP_SCHEDULER_TASK_PERIOD,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldCheckbox('is_strict_period', [
                        'title' => LANG_CP_SCHEDULER_IS_STRICT_PERIOD
                    ]),
                    new fieldDate('date_last_run', [
                        'title' => LANG_CP_SCHEDULER_TASK_LAST_RUN,
                        'options' => [
                            'show_time' => true
                        ]
                    ]),
                    new fieldCheckbox('is_active', [
                        'title' => LANG_CP_SCHEDULER_TASK_IS_ACTIVE
                    ])
                ]
            ]
        ];
    }

}
