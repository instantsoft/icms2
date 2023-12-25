<?php

class formWysiwygsPreset extends cmsForm {

    public function init($do) {

        return [
            'basic' => [
                'title'  => LANG_BASIC_OPTIONS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_WW_PRESET_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 128]
                        ]
                    ]),
                    new fieldList('wysiwyg_name', [
                        'title'     => LANG_PARSER_HTML_EDITOR,
                        'generator' => function ($item) {
                            $items   = ['' => ''];
                            $editors = cmsCore::getWysiwygs();
                            foreach ($editors as $editor) {

                                $form_file = 'wysiwyg/' . $editor . '/options.php';

                                if (file_exists(cmsConfig::get('root_path') . $form_file)) {
                                    $items[$editor] = $editor;
                                }
                            }
                            return $items;
                        },
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }
}
