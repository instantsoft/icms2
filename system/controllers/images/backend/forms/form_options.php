<?php

class formImagesOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(
        
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_IMAGES_MINMAX,
                'childs' => array(
                    new fieldNumber('image_minwidth', array(
                        'title' => LANG_CP_SETTINGS_IMAGES_MINWIDTH,
                    )),
                    new fieldNumber('image_minheight', array(
                        'title' => LANG_CP_SETTINGS_IMAGES_MINHEIGHT,
                    )),
                )
            ),
            
        );

    }

}
