<?php

class formModernTemplateOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $gamma = [
            '$white'    => LANG_MODERN_C_WHITE,
            '$gray-100' => LANG_MODERN_C_GRAY100,
            '$gray-200' => LANG_MODERN_C_GRAY200,
            '$gray-300' => LANG_MODERN_C_GRAY300,
            '$gray-400' => LANG_MODERN_C_GRAY400,
            '$gray-500' => LANG_MODERN_C_GRAY500,
            '$gray-600' => LANG_MODERN_C_GRAY600,
            '$gray-700' => LANG_MODERN_C_GRAY700,
            '$gray-800' => LANG_MODERN_C_GRAY800,
            '$gray-900' => LANG_MODERN_C_GRAY900,
            '$black'    => LANG_MODERN_C_BLACK,
            '$blue'     => LANG_MODERN_C_BLUE,
            '$indigo'   => LANG_MODERN_C_INDIGO,
            '$purple'   => LANG_MODERN_C_PURPLE,
            '$pink'     => LANG_MODERN_C_PINK,
            '$red'      => LANG_MODERN_C_RED,
            '$orange'   => LANG_MODERN_C_ORANGE,
            '$yellow'   => LANG_MODERN_C_YELLOW,
            '$green'    => LANG_MODERN_C_GREEN,
            '$teal'     => LANG_MODERN_C_TEAL,
            '$cyan'     => LANG_MODERN_C_CYAN
        ];

        $theme_colors = [
            '$primary'   => LANG_MODERN_C_PRIMARY,
            '$secondary' => LANG_MODERN_C_SECONDARY,
            '$success'   => LANG_MODERN_C_SUCCESS,
            '$info'      => LANG_MODERN_C_INFO,
            '$warning'   => LANG_MODERN_C_WARNING,
            '$danger'    => LANG_MODERN_C_DANGER,
            '$light'     => LANG_MODERN_C_LIGHT,
            '$dark'      => LANG_MODERN_C_DARK
        ];

        $fields = array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => array(

                    new fieldString('owner_name', array(
                        'title' => LANG_MODERN_THEME_COPYRIGHT
                    )),

                    new fieldString('owner_url', array(
                        'title' => LANG_MODERN_THEME_COPYRIGHT_URL,
                        'hint' => LANG_MODERN_THEME_COPYRIGHT_URL_HINT
                    )),

                    new fieldString('owner_year', array(
                        'title' => LANG_MODERN_THEME_COPYRIGHT_YEAR,
                        'hint' => LANG_MODERN_THEME_COPYRIGHT_YEAR_HINT
                    )),

                    new fieldCheckbox('scss:enable-rounded', array(
                        'title' => LANG_MODERN_THEME_ENABLE_ROUNDED,
                        'default' => 1
                    )),

                    new fieldString('scss:border-radius', array(
                        'title' => LANG_MODERN_THEME_ROUNDED_BASE,
                        'visible_depend' => array('scss:enable-rounded' => array('show' => array('1')))
                    )),

                    new fieldString('scss:border-radius-lg', array(
                        'title' => LANG_MODERN_THEME_ROUNDED_BASE_LG,
                        'visible_depend' => array('scss:enable-rounded' => array('show' => array('1')))
                    )),

                    new fieldString('scss:border-radius-sm', array(
                        'title' => LANG_MODERN_THEME_ROUNDED_BASE_SM,
                        'visible_depend' => array('scss:enable-rounded' => array('show' => array('1')))
                    )),

                    new fieldCheckbox('scss:enable-shadows', array(
                        'title' => LANG_MODERN_THEME_ENABLE_SHADOWS
                    )),

                    new fieldCheckbox('scss:enable-gradients', array(
                        'title' => LANG_MODERN_THEME_ENABLE_GRADIENTS
                    )),

                    new fieldCheckbox('scss:enable-responsive-font-sizes', array(
                        'title' => LANG_MODERN_THEME_ENABLE_RFS
                    )),

                    new fieldCheckbox('scss:enable-modal-blur', array(
                        'title' => LANG_MODERN_THEME_ENABLE_MODAL_BLUR
                    )),

                    new fieldString('scss:grid-gutter-width', array(
                        'title' => LANG_MODERN_THEME_GRID_GUTTER_W,
                        'default' => '30px'
                    )),

                    new fieldString('scss:font-size-base', array(
                        'title' => LANG_MODERN_THEME_BASE_FS,
                        'default' => '1rem'
                    )),

                    new fieldList('pagination_template', array(
                        'title' => LANG_MODERN_THEME_PAGINATION_TPL,
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'pagination*.tpl.php', 'modern');
                        }
                    ))

                )
            ),

            'colors' => array(
                'type' => 'fieldset',
                'title' => LANG_MODERN_THEME_COLORS,
                'childs' => [
                    new fieldList('scss:body-bg', array(
                        'title' => LANG_MODERN_THEME_BGCOLOR,
                        'hint' => LANG_MODERN_THEME_GCOLOR,
                        'items' => ['' => LANG_MODERN_THEME_SET_MY_COLOR]+$gamma
                    )),
                    new fieldColor('custom_scss:body-bg', array(
                        'visible_depend' => array('scss:body-bg' => array('show' => array('')))
                    )),
                    new fieldList('scss:body-color', array(
                        'title' => LANG_MODERN_THEME_BCOLOR,
                        'hint' => LANG_MODERN_THEME_GCOLOR,
                        'items' => ['' => LANG_MODERN_THEME_SET_MY_COLOR]+$gamma
                    )),
                    new fieldColor('custom_scss:body-color', array(
                        'visible_depend' => array('scss:body-color' => array('show' => array('')))
                    )),
                ]
            ),

            'gamma' => array(
                'type' => 'fieldset',
                'title' => LANG_MODERN_THEME_GAMMA,
                'childs' => []
            )

        );

        foreach ($gamma as $name => $title) {
            $fields['gamma']['childs'][] = new fieldColor('scss:'.str_replace('$', '', $name), array(
                'title' => $title
            ));
        }

        foreach ($theme_colors as $name => $title) {
            $name = str_replace('$', '', $name);
            $fields['colors']['childs'][] = new fieldList('scss:'.$name, array(
                'title' => $title,
                'hint' => LANG_MODERN_THEME_GCOLOR,
                'items' => ['' => LANG_MODERN_THEME_SET_MY_COLOR]+$gamma
            ));
            $fields['colors']['childs'][] = new fieldColor('custom_scss:'.$name, array(
                'visible_depend' => array('scss:'.$name => array('show' => array('')))
            ));
        }

        return $fields;
    }

}
