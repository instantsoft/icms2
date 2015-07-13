<?php

class formMarkitupOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_MARKITUP_THEME,
                'childs' => array(

                    new fieldString('set', array(
                        'title' => LANG_MARKITUP_THEME_SET,
                        'default' => 'default_ru'
                    )),
                    new fieldString('skin', array(
                        'title' => LANG_MARKITUP_THEME_SKIN,
                        'default' => 'simple'
                    )),

                )
            ),

        );

    }

}
