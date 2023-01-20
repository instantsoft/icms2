<?php

class formWidgetTextOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldHtml('options:content', [
                        'title' => LANG_WD_TEXT_CONTENT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'unset_required' => true
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
