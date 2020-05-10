<?php

class formTemplateProfileOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_DEFAULT_THEME_BG,
                'childs' => array(

                    new fieldImage('bg_img', array(
                        'title' => LANG_DEFAULT_THEME_BG_IMAGE,
                        'hint' => LANG_DEFAULT_THEME_BG_IMAGE_HINT,
                        'options' => array(
                            'sizes' => array('small', 'original')
                        )
                    )),

                    new fieldColor('bg_color', array(
                        'title' => LANG_DEFAULT_THEME_BG_COLOR,
                        'default' => '#FFFFFF',
                        'options' => array(
                            'opacity' => true
                        )
                    )),

                    new fieldList('bg_repeat', array(
                        'title' => LANG_DEFAULT_THEME_BG_REPEAT,
                        'default' => 'repeat',
                        'items' => array(
                            'repeat' => LANG_DEFAULT_THEME_BG_REPEAT_XY,
                            'no-repeat' => LANG_DEFAULT_THEME_BG_REPEAT_NO,
                            'repeat-x' => LANG_DEFAULT_THEME_BG_REPEAT_X,
                            'repeat-y' => LANG_DEFAULT_THEME_BG_REPEAT_Y,
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_DEFAULT_THEME_BG_POS,
                'childs' => array(

                    new fieldList('bg_pos_x', array(
                        'title' => LANG_DEFAULT_THEME_BG_POS_X,
                        'default' => 'left',
                        'items' => array(
                            'left' => LANG_DEFAULT_THEME_BG_POS_X_LEFT,
                            'center' => LANG_DEFAULT_THEME_BG_POS_X_CENTER,
                            'right' => LANG_DEFAULT_THEME_BG_POS_X_RIGHT,
                        )
                    )),

                    new fieldList('bg_pos_y', array(
                        'title' => LANG_DEFAULT_THEME_BG_POS_Y,
                        'default' => 'top',
                        'items' => array(
                            'top' => LANG_DEFAULT_THEME_BG_POS_Y_TOP,
                            'center' => LANG_DEFAULT_THEME_BG_POS_Y_CENTER,
                            'bottom' => LANG_DEFAULT_THEME_BG_POS_Y_BOTTOM,
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_DEFAULT_THEME_TOP_MARGIN,
                'childs' => array(

                    new fieldNumber('margin_top', array(
                        'default' => 0,
                        'rules' => array(
                           array('min', 0),
                           array('max', 150),
                        )
                    )),

                )
            ),

        );

    }

}
