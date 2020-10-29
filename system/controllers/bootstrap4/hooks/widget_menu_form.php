<?php

class onBootstrap4WidgetMenuForm extends cmsAction {

	public function run($_data){

        list($form, $widget, $widget_object, $template) = $_data;

        // Нам нужен только шаблон modern
        if($template !== 'modern'){
            return $_data;
        }

        $form->addField('menu_options', new fieldList('options:menu_type', array(
            'title' => LANG_BS4_MENU_TYPE,
            'items' => [
                'navbar' => LANG_BS4_MENU_TYPE_NAVBAR,
                'nav' => LANG_BS4_MENU_TYPE_NAV
            ]
        )));

        // ****************************************************************** //
        //                  Опции навигационной панели                        //
        // ****************************************************************** //
        $form->addField('menu_options', new fieldList('options:navbar_expand', array(
            'title' => LANG_BS4_NAVBAR_EXPAND,
            'default' => 'navbar-expand-lg',
            'items' => [
                ''  => LANG_BS4_ALWAYS_SHOW,
                'navbar-expand-sm' => sprintf(LANG_BS4_NAVBAR_EXPAND_ON, '≥576px'),
                'navbar-expand-md' => sprintf(LANG_BS4_NAVBAR_EXPAND_ON, '≥768px'),
                'navbar-expand-lg' => sprintf(LANG_BS4_NAVBAR_EXPAND_ON, '≥992px'),
                'navbar-expand-xl' => sprintf(LANG_BS4_NAVBAR_EXPAND_ON, '≥1200px')
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldList('options:navbar_color_scheme', array(
            'title' => LANG_BS4_NAVBAR_COLOR_SCHEME,
            'items' => [
                'navbar-dark' => LANG_BS4_NAVBAR_COLOR_SCHEME_D,
                'navbar-light' => LANG_BS4_NAVBAR_COLOR_SCHEME_L
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldList('options:menu_navbar_style', array(
            'title' => LANG_BS4_MENU_NAV_STYLE,
            'items' => [
                'navbar-nav' => LANG_BS4_MENU_NAV_STYLE_HL,
                'navbar-nav justify-content-center'    => LANG_BS4_MENU_NAV_STYLE_HC,
                'navbar-nav justify-content-sm-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥576px',
                'navbar-nav justify-content-md-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥768px',
                'navbar-nav justify-content-lg-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥992px',
                'navbar-nav justify-content-xl-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥1200px',
                'navbar-nav justify-content-end'       => LANG_BS4_MENU_NAV_STYLE_HR,
                'navbar-nav justify-content-sm-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥576px',
                'navbar-nav justify-content-md-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥768px',
                'navbar-nav justify-content-lg-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥992px',
                'navbar-nav justify-content-xl-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥1200px',
                'navbar-nav flex-column'               => LANG_BS4_MENU_NAV_STYLE_V
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldList('options:show_search_form', array(
            'title' => LANG_BS4_SHOW_SEARCH_FORM,
            'items' => [
                0 => LANG_CP_FIELD_LABEL_NONE,
                1 => LANG_BS4_ALWAYS_SHOW,
                2 => LANG_BS4_SHOW_SEARCH_FORM_2
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldCheckbox('options:toggler_icon', array(
            'title' => LANG_BS4_TOGGLER_ICON,
            'hint' => LANG_BS4_TOGGLER_ICON_HINT,
            'default' => 1,
            'visible_depend' => ['options:menu_type' => ['show' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldCheckbox('options:toggler_show_sitename', array(
            'title' => LANG_BS4_TOGGLER_SHOW_SITENAME,
            'visible_depend' => ['options:toggler_icon' => ['show' => ['1']], 'options:menu_type' => ['hide' => ['nav']]]
        )));

        // ****************************************************************** //
        //                     Опции обычного меню                            //
        // ****************************************************************** //
        $form->addField('menu_options', new fieldList('options:menu_nav_style', array(
            'title' => LANG_BS4_MENU_NAV_STYLE,
            'items' => [
                'nav' => LANG_BS4_MENU_NAV_STYLE_HL,
                'nav justify-content-center'    => LANG_BS4_MENU_NAV_STYLE_HC,
                'nav justify-content-sm-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥576px',
                'nav justify-content-md-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥768px',
                'nav justify-content-lg-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥992px',
                'nav justify-content-xl-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥1200px',
                'nav justify-content-end'       => LANG_BS4_MENU_NAV_STYLE_HR,
                'nav justify-content-sm-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥576px',
                'nav justify-content-md-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥768px',
                'nav justify-content-lg-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥992px',
                'nav justify-content-xl-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥1200px',
                'nav flex-column'               => LANG_BS4_MENU_NAV_STYLE_V
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['nav']]]
        )));

        $form->addField('menu_options', new fieldList('options:menu_nav_style_column', array(
            'title' => LANG_BS4_MENU_NAV_STYLE_COLUMN,
            'items' => [
                '' => LANG_CP_NEVER,
                'flex-sm-column' => '≥576px',
                'flex-md-column' => '≥768px',
                'flex-lg-column' => '≥992px',
                'flex-xl-column' => '≥1200px'
            ],
            'visible_depend' => ['options:menu_nav_style' => ['show' => ['nav','nav justify-content-center','nav justify-content-end']], 'options:menu_type' => ['hide' => ['navbar']]]
        )));

        $form->addField('menu_options', new fieldCheckbox('options:menu_is_pills', array(
            'title' => LANG_BS4_MENU_IS_PILLS,
            'visible_depend' => ['options:menu_type' => ['show' => ['nav']]]
        )));

        $form->addField('menu_options', new fieldList('options:menu_is_fill', array(
            'title' => LANG_BS4_MENU_IS_FILL,
            'items' => [
                '' => LANG_NO,
                'nav-fill' => LANG_AUTO,
                'nav-justified' => LANG_BS4_MENU_IS_FILL_JUS
            ],
            'visible_depend' => ['options:menu_type' => ['show' => ['nav']]]
        )));

        return [$form, $widget, $widget_object, $template];
    }

}
