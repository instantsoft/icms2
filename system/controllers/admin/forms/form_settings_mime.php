<?php

class formAdminSettingsMime extends cmsForm {


    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldFieldsgroup('mimetypes', [
                        'title'  => LANG_CP_MIMETYPES,
                        'childs' => [
                            new fieldString('extension', [
                                'attributes' => ['placeholder' => LANG_CP_EXTENSION],
                                'rules'      => [
                                    ['required'],
                                    ['alphanumeric']
                                ]
                            ]),
                            new fieldText('mimes', [
                                'attributes' => [
                                    'placeholder' => LANG_CP_MIMETYPE,
                                    'class' => 'h-auto',
                                ],
                                'is_strip_tags' => true,
                                'options'    => [
                                    'size' => 2
                                ],
                                'rules'      => [
                                    ['required'],
                                    ['regexp', '/^([a-z0-9\/\.\-\+]+)$/mi', LANG_CP_MIMETYPE_ERROR]
                                ]
                            ])
                        ]
                    ])
                ]
            ]
        ];
    }

}
