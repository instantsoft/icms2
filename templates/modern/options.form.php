<?php

class formModernTemplateOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(

            array(
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

                    new fieldCheckbox('scss:enable-shadows', array(
                        'title' => LANG_MODERN_THEME_ENABLE_SHADOWS
                    )),

                    new fieldCheckbox('scss:enable-gradients', array(
                        'title' => LANG_MODERN_THEME_ENABLE_GRADIENTS
                    )),

                    new fieldNumber('scss:grid-gutter-width', array(
                        'title' => 'grid-gutter-width',
                        'units' => 'px',
                        'default' => 30
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_MODERN_THEME_COLORS,
                'childs' => array(
                    new fieldColor('scss:white', array(
                        'title' => 'white',
                        'default' => '#fff'
                    )),
                    new fieldColor('scss:gray-100', array(
                        'title' => 'gray-100',
                        'default' => '#f8f9fa'
                    )),
                    new fieldColor('scss:gray-200', array(
                        'title' => 'gray-200',
                        'default' => '#e9ecef'
                    )),
                    new fieldColor('scss:gray-300', array(
                        'title' => 'gray-300',
                        'default' => '#dee2e6'
                    )),
                    new fieldColor('scss:gray-400', array(
                        'title' => 'gray-400',
                        'default' => '#ced4da'
                    )),
                    new fieldColor('scss:gray-500', array(
                        'title' => 'gray-500',
                        'default' => '#adb5bd'
                    )),
                    new fieldColor('scss:gray-600', array(
                        'title' => 'gray-600',
                        'default' => '#6c757d'
                    )),
                    new fieldColor('scss:gray-700', array(
                        'title' => 'gray-700',
                        'default' => '#495057'
                    )),
                    new fieldColor('scss:gray-800', array(
                        'title' => 'gray-800',
                        'default' => '#343a40'
                    )),
                    new fieldColor('scss:gray-900', array(
                        'title' => 'gray-900',
                        'default' => '#212529'
                    )),
                    new fieldColor('scss:black', array(
                        'title' => 'black',
                        'default' => '#000'
                    )),
                    new fieldColor('scss:blue', array(
                        'title' => 'blue',
                        'default' => '#007bff'
                    )),
                    new fieldColor('scss:indigo', array(
                        'title' => 'indigo',
                        'default' => '#6610f2'
                    )),
                    new fieldColor('scss:purple', array(
                        'title' => 'purple',
                        'default' => '#6f42c1'
                    )),
                    new fieldColor('scss:pink', array(
                        'title' => 'pink',
                        'default' => '#e83e8c'
                    )),
                    new fieldColor('scss:red', array(
                        'title' => 'red',
                        'default' => '#dc3545'
                    )),
                    new fieldColor('scss:orange', array(
                        'title' => 'orange',
                        'default' => '#fd7e14'
                    )),
                    new fieldColor('scss:yellow', array(
                        'title' => 'yellow',
                        'default' => '#ffc107'
                    )),
                    new fieldColor('scss:green', array(
                        'title' => 'green',
                        'default' => '#28a745'
                    )),
                    new fieldColor('scss:teal', array(
                        'title' => 'teal',
                        'default' => '#20c997'
                    )),
                    new fieldColor('scss:cyan', array(
                        'title' => 'cyan',
                        'default' => '#17a2b8'
                    ))
                )
            )

        );

    }

}
