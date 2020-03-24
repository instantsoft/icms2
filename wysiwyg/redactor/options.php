<?php
class formWysiwygRedactorOptions extends cmsForm {

    public function init($do) {

        $buttons = ['html', 'undo', 'redo', 'formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'image', 'video', 'table', 'link', 'alignment', '|', 'horizontalrule', 'underline', 'alignleft', 'aligncenter', 'alignright', 'justify'];

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_WW_OPTIONS,
                'childs' => array(

                    new fieldList('options:plugins', array(
                        'title' => LANG_REDACTOR_PLUGINS,
                        'is_chosen_multiple' => true,
                        'generator' => function($item){
                            $items = [];
                            $ps = cmsCore::getDirsList('wysiwyg/redactor/files/plugins');
                            foreach($ps as $p){ $items[$p] = $p; }
                            return $items;
                        },
                        'default' => ['smiles', 'spoiler']
                    )),

                    new fieldList('options:buttons', array(
                        'title' => LANG_REDACTOR_BUTTONS,
                        'is_chosen_multiple' => true,
                        'items' => array_combine($buttons, $buttons),
                        'default' => ['html', 'undo', 'redo', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'image', 'video', 'table', 'link', 'alignment']
                    )),

                    new fieldCheckbox('options:convertVideoLinks', array(
                        'title' => LANG_REDACTOR_CONVERTVIDEOLINKS,
                        'default' => true
                    )),

                    new fieldCheckbox('options:convertDivs', array(
                        'title' => LANG_REDACTOR_CONVERTDIVS,
                        'default' => false
                    )),

                    new fieldCheckbox('options:toolbarFixedBox', array(
                        'title' => LANG_REDACTOR_TOOLBARFIXEDBOX,
                        'default' => true
                    )),

                    new fieldCheckbox('options:autoresize', array(
                        'title' => LANG_REDACTOR_AUTORESIZE,
                        'default' => true
                    )),

                    new fieldCheckbox('options:pastePlainText', array(
                        'title' => LANG_REDACTOR_PASTEPLAINTEXT,
                        'default' => false
                    )),

                    new fieldCheckbox('options:removeEmptyTags', array(
                        'title' => LANG_REDACTOR_REMOVEEMPTYTAGS,
                        'default' => true
                    )),

                    new fieldCheckbox('options:linkNofollow', array(
                        'title' => LANG_REDACTOR_LINKNOFOLLOW,
                        'default' => false
                    )),

                    new fieldNumber('options:minHeight', array(
                        'title' => LANG_REDACTOR_MINHEIGHT,
                        'default' => 200
                    )),

                    new fieldString('options:placeholder', array(
                        'title' => LANG_REDACTOR_PLACEHOLDER
                    ))

                )
            )

        );

    }

}
