<?php
class formWysiwygTinymceOptions extends cmsForm {

    private $mimetypes;

    public function init($do) {

        cmsCore::includeFile('wysiwyg/tinymce/wysiwyg.class.php');

        $editor = new cmsWysiwygTinymce();

        list($plugins, $buttons, $quickbars_insert_buttons, $quickbars_selection_buttons, $block_formats) = $editor->getParams();

        asort($buttons); asort($plugins); asort($quickbars_insert_buttons); asort($quickbars_selection_buttons);

        $groups = cmsCore::getModel('users')->getGroups(false);

        $childs = array(

            new fieldText('options:toolbar', array(
                'title' => LANG_TINYMCE_TOOLBAR,
                'patterns_hint' => [
                    'patterns' =>  $buttons,
                    'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN,
                    'wrap_symbols' =>  ['','']
                ],
                'default' => 'formatselect | bold italic forecolor | link image',
                'options'=>array(
                    'max_length' => 1000
                )
            )),

            new fieldString('options:quickbars_selection_toolbar', array(
                'title' => LANG_TINYMCE_QUICKBARS_SELECTION_TOOLBAR,
                'patterns_hint' => [
                    'patterns' =>  $quickbars_selection_buttons,
                    'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN,
                    'wrap_symbols' =>  ['','']
                ],
                'default' => 'bold italic underline | quicklink h2 h3 blockquote'
            )),

            new fieldString('options:quickbars_insert_toolbar', array(
                'title' => LANG_TINYMCE_QUICKBARS_INSERT_TOOLBAR,
                'patterns_hint' => [
                    'patterns' =>  $quickbars_insert_buttons,
                    'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN,
                    'wrap_symbols' =>  ['','']
                ],
                'default' => 'quickimage quicktable'
            )),

            new fieldList('options:plugins', array(
                'title' => LANG_TINYMCE_PLUGINS,
                'hint'  => LANG_TINYMCE_PLUGINS_HINT,
                'is_chosen_multiple' => true,
                'items' => array_combine($plugins, $plugins),
                'default' => 'autoresize'
            )),

            new fieldList('options:skin', array(
                'title' => LANG_TINYMCE_SKIN,
                'default' => 'oxide',
                'generator' => function($item){
                    $items = [];
                    $ps = cmsCore::getDirsList('wysiwyg/tinymce/files/skins/ui');
                    foreach($ps as $p){ $items[$p] = $p; }
                    return $items;
                }
            )),

            new fieldList('options:forced_root_block', array(
                'title' => LANG_TINYMCE_FORCED_ROOT_BLOCK,
                'items' => [
                    '' => sprintf(LANG_TINYMCE_TAG, '<br>'),
                    'p' => sprintf(LANG_TINYMCE_TAG, '<p>'),
                    'div' => sprintf(LANG_TINYMCE_TAG, '<div>')
                ],
                'default' => 'p',
            )),

            new fieldList('options:block_formats', array(
                'title' => LANG_TINYMCE_BLOCK_FORMATS,
                'is_chosen_multiple' => true,
                'items' => $block_formats,
                'default' => ['p','h2','h3']
            )),

            new fieldList('options:toolbar_drawer', array(
                'title' => LANG_TINYMCE_TOOLBAR_DRAWER,
                'items' => [
                    '' => LANG_TINYMCE_TOOLBAR_DRAWER0,
                    'floating' => LANG_TINYMCE_TOOLBAR_DRAWER1,
                    'sliding' => LANG_TINYMCE_TOOLBAR_DRAWER2
                ]
            )),

            new fieldCheckbox('options:image_caption', array(
                'title' => LANG_TINYMCE_IMAGE_CAPTION,
                'default' => false
            )),

            new fieldCheckbox('options:image_title', array(
                'title' => LANG_TINYMCE_IMAGE_TITLE,
                'default' => false
            )),

            new fieldCheckbox('options:image_description', array(
                'title' => LANG_TINYMCE_IMAGE_DESCRIPTION,
                'default' => false
            )),

            new fieldCheckbox('options:image_dimensions', array(
                'title' => LANG_TINYMCE_IMAGE_DIMENSIONS,
                'default' => false
            )),

            new fieldCheckbox('options:image_advtab', array(
                'title' => LANG_TINYMCE_IMAGE_ADVTAB,
                'default' => false
            )),

            new fieldCheckbox('options:statusbar', array(
                'title' => LANG_TINYMCE_STATUSBAR,
                'default' => false
            )),

            new fieldCheckbox('options:paste_as_text', array(
                'title' => LANG_TINYMCE_PASTE_AS_TEXT,
                'default' => false
            )),

            new fieldNumber('options:min_height', array(
                'title' => LANG_TINYMCE_MIN_HEIGHT,
                'default' => 350
            )),

            new fieldNumber('options:max_height', array(
                'title' => LANG_TINYMCE_MAX_HEIGHT,
                'default' => 700
            )),

            new fieldList('options:images_preset', array(
                'title'     => LANG_TINYMCE_IMG_PRESET,
                'generator' => function (){
                    return cmsCore::getModel('images')->getPresetsList(true);
                },
                'default' => 'big'
            ))

        );

        foreach ($groups as $group) {
            $childs[] = new fieldList('options:allow_mime_types:'.$group['id'], array(
                'title' => sprintf(LANG_TINYMCE_ALLOW_MIME_TYPES, $group['title']),
                'is_chosen_multiple' => true,
                'items' => $this->getMimeTypes()
            ));
        }

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_WW_OPTIONS,
                'childs' => $childs
            )

        );

    }

    private function getMimeTypes() {

        if($this->mimetypes === null){
            $this->mimetypes = (new cmsConfigs('mimetypes.php'))->getAll();
        }

        return $this->mimetypes;
    }

}
