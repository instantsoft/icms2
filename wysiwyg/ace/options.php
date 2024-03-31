<?php

class formWysiwygAceOptions extends cmsForm {

    public function init($do) {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_WW_OPTIONS,
                'childs' => [
                    new fieldList('options:theme', [
                        'title'     => LANG_ACE_THEME,
                        'generator' => function ($item) {
                            $items = [];
                            $ps    = cmsCore::getFilesList('wysiwyg/ace/files/', 'theme-*', true);
                            foreach ($ps as $p) {
                                $name = str_replace('theme-', '', $p);
                                $items['ace/theme/' . $name] = ucfirst(str_replace('_', ' ', $name));
                            }
                            return $items;
                        },
                        'default' => 'ace/theme/github_dark'
                    ]),
                    new fieldNumber('options:fontSize', [
                        'title'   => LANG_ACE_FONTSIZE,
                        'units'   => 'px',
                        'default' => 12
                    ]),
                    new fieldCheckbox('options:enableSnippets', [
                        'title'   => LANG_ACE_ENABLESNIPPETS,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:enableEmmet', [
                        'title'   => LANG_ACE_ENABLEEMMET,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:enableBasicAutocompletion', [
                        'title'   => LANG_ACE_ENABLEBASICAUTOCOMPLETION,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:enableLiveAutocompletion', [
                        'title'   => LANG_ACE_ENABLELIVEAUTOCOMPLETION,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:wrap', [
                        'title'   => LANG_ACE_WRAP,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:showInvisibles', [
                        'title'   => LANG_ACE_SHOWINVISIBLES,
                        'default' => false
                    ]),
                    new fieldCheckbox('options:showGutter', [
                        'title'   => LANG_ACE_SHOWGUTTER,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:showLineNumbers', [
                        'title'   => LANG_ACE_SHOWLINENUMBERS,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:displayIndentGuides', [
                        'title'   => LANG_ACE_DISPLAYINDENTGUIDES,
                        'default' => true
                    ])
                ]
            ]
        ];
    }

}
