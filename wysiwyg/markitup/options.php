<?php
class formWysiwygMarkitupOptions extends cmsForm {

    public function init($do) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_WW_OPTIONS,
                'childs' => [
                    new fieldList('options:buttons', [
                        'title' => LANG_MARKITUP_BTN,
                        'is_chosen_multiple' => true,
                        'generator' => function($item) {

                            $editor = cmsWysiwyg::getEditor('markitup');

                            $items = [];

                            foreach ($editor->default_set['markupSet'] as $id => $btn) {
                                $items[$id] = $btn['name'];
                            }

                            return $items;
                        },
                        'default' => [0,1,2,3,9,14]
                    ]),
                    new fieldList('options:skin', [
                        'title' => LANG_MARKITUP_THEME_SKIN,
                        'generator' => function($item){
                            $items = [];
                            $ps = cmsCore::getDirsList('wysiwyg/markitup/skins');
                            foreach($ps as $p){ $items[$p] = $p; }
                            return $items;
                        },
                        'default' => 'simple'
                    ])
                ]
            ]
        ];
    }

}
