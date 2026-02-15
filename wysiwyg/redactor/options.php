<?php
class formWysiwygRedactorOptions extends cmsForm {

    public function init($do) {

        $buttons = ['html', 'undo', 'redo', 'formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'image', 'video', 'table', 'link', 'alignment', '|', 'horizontalrule', 'underline', 'alignleft', 'aligncenter', 'alignright', 'justify'];

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_WW_OPTIONS,
                'childs' => [
                    new fieldList('options:plugins', [
                        'title' => LANG_REDACTOR_PLUGINS,
                        'is_chosen_multiple' => true,
                        'generator' => function($item) {
                            $items = [];
                            $ps = cmsCore::getDirsList('wysiwyg/redactor/files/plugins');
                            foreach($ps as $p) { $items[$p] = $p; }
                            return $items;
                        },
                        'default' => ['smiles', 'spoiler']
                    ]),
                    new fieldList('options:buttons', [
                        'title' => LANG_REDACTOR_BUTTONS,
                        'is_chosen_multiple' => true,
                        'items' => array_combine($buttons, $buttons),
                        'default' => ['html', 'undo', 'redo', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'image', 'video', 'table', 'link', 'alignment']
                    ]),
                    new fieldCheckbox('options:convertVideoLinks', [
                        'title' => LANG_REDACTOR_CONVERTVIDEOLINKS,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:convertDivs', [
                        'title' => LANG_REDACTOR_CONVERTDIVS,
                        'default' => false
                    ]),
                    new fieldCheckbox('options:toolbarFixedBox', [
                        'title' => LANG_REDACTOR_TOOLBARFIXEDBOX,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:autoresize', [
                        'title' => LANG_REDACTOR_AUTORESIZE,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:pastePlainText', [
                        'title' => LANG_REDACTOR_PASTEPLAINTEXT,
                        'default' => false
                    ]),
                    new fieldCheckbox('options:removeEmptyTags', [
                        'title' => LANG_REDACTOR_REMOVEEMPTYTAGS,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:linkNofollow', [
                        'title' => LANG_REDACTOR_LINKNOFOLLOW,
                        'default' => false
                    ]),
                    new fieldNumber('options:minHeight', [
                        'title' => LANG_REDACTOR_MINHEIGHT,
                        'default' => 200
                    ]),
                    new fieldString('options:placeholder', [
                        'title' => LANG_REDACTOR_PLACEHOLDER
                    ])
                ]
            ]
        ];
    }

}
