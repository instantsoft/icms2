<?php

class formPhotosOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_origs', array(
                        'title' => LANG_PHOTOS_SAVE_ORIG,
                        'hint' => LANG_PHOTOS_SAVE_ORIG_HINT,
                    )),

                    new fieldList('preset', array(
                        'title' => LANG_PHOTOS_PRESET,
                        'generator' => function($item) {
                            return array('' => LANG_PHOTOS_PRESET_DEF) + cmsCore::getModel('images')->getPresetsList();
                        }
                    )),

                )
            ),

        );

    }

}