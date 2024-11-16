<?php

class formContentChangeOwner extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_OWNER_NEW_EMAIL,
                'childs' => [
                    new fieldString('email', [
                        'hint'  => LANG_OWNER_NEW_EMAIL_HINT,
                        'rules' => [
                            ['required'],
                            ['email']
                        ]
                    ])
                ]
            ]
        ];
    }

}
