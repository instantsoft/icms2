<?php

class onBootstrap4WidgetMenuForm extends cmsAction {

	public function run($_data){

        list($form, $widget, $widget_object, $template_name) = $_data;

        $template = new cmsTemplate($template_name);

        $manifest = $template->getManifest();

        if(empty($manifest['properties']['vendor'])){
            return $_data;
        }

        // Нам нужны только шаблоны на bootstrap4
        if($manifest['properties']['vendor'] !== 'bootstrap4'){
            return $_data;
        }

        $form->addField('menu_options', new fieldList('options:navbar_color_scheme', array(
            'title' => LANG_BS4_NAVBAR_COLOR_SCHEME,
            'items' => [
                '' => LANG_BY_DEFAULT,
                'navbar-dark' => LANG_BS4_NAVBAR_COLOR_SCHEME_D,
                'navbar-light' => LANG_BS4_NAVBAR_COLOR_SCHEME_L
            ]
        )));

        $form->addField('menu_options', new fieldList('options:menu_nav_style', array(
            'title' => LANG_BS4_MENU_NAV_STYLE,
            'items' => [
                '' => LANG_BS4_MENU_NAV_STYLE_HL,
                'justify-content-between' => LANG_BS4_MENU_NAV_STYLE_HB,
                'justify-content-center'  => LANG_BS4_MENU_NAV_STYLE_HC,
                'justify-content-end'     => LANG_BS4_MENU_NAV_STYLE_HR,
                'flex-column'             => LANG_BS4_MENU_NAV_STYLE_V
            ]
        )));

        $form->addField('menu_options', new fieldList('options:menu_nav_style_add', array(
            'title' => LANG_BS4_MENU_NAV_STYLE_ADD,
            'items' => [
                '' => '',
                'flex-sm-row justify-content-sm-start'  => LANG_BS4_MENU_NAV_STYLE_HL.' ≥576px',
                'flex-md-row justify-content-md-start'  => LANG_BS4_MENU_NAV_STYLE_HL.' ≥768px',
                'flex-lg-row justify-content-lg-start'  => LANG_BS4_MENU_NAV_STYLE_HL.' ≥992px',
                'flex-xl-row justify-content-xl-start'  => LANG_BS4_MENU_NAV_STYLE_HL.' ≥1200px',
                'flex-sm-row justify-content-sm-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥576px',
                'flex-md-row justify-content-md-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥768px',
                'flex-lg-row justify-content-lg-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥992px',
                'flex-xl-row justify-content-xl-center' => LANG_BS4_MENU_NAV_STYLE_HC.' ≥1200px',
                'flex-sm-row justify-content-sm-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥576px',
                'flex-md-row justify-content-md-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥768px',
                'flex-lg-row justify-content-lg-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥992px',
                'flex-xl-row justify-content-xl-end'    => LANG_BS4_MENU_NAV_STYLE_HR.' ≥1200px',
                'flex-sm-column' => LANG_BS4_MENU_NAV_STYLE_V.' ≥576px',
                'flex-md-column' => LANG_BS4_MENU_NAV_STYLE_V.' ≥768px',
                'flex-lg-column' => LANG_BS4_MENU_NAV_STYLE_V.' ≥992px',
                'flex-xl-column' => LANG_BS4_MENU_NAV_STYLE_V.' ≥1200px'
            ]
        )));

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

        $form->addField('menu_options', new fieldCheckbox('options:toggler_show_logo', array(
            'title' => LANG_BS4_TOGGLER_SHOW_LOGO,
            'visible_depend' => ['options:toggler_icon' => ['show' => ['1']], 'options:menu_type' => ['hide' => ['nav']]]
        )));

        $form->addField('menu_options', new fieldCheckbox('options:toggler_right_menu', array(
            'title' => LANG_BS4_TOGGLER_RIGHT_MENU,
            'visible_depend' => ['options:toggler_icon' => ['show' => ['1']], 'options:toggler_show_logo' => ['hide' => ['0']], 'options:toggler_icon' => ['hide' => ['0']], 'options:menu_type' => ['hide' => ['nav']]]
        )));

        $form->addField('menu_options', new fieldCheckbox('options:toggler_show_sitename', array(
            'title' => LANG_BS4_TOGGLER_SHOW_SITENAME,
            'visible_depend' => ['options:toggler_icon' => ['show' => ['1']], 'options:menu_type' => ['hide' => ['nav']]]
        )));

        // ****************************************************************** //
        //                     Опции обычного меню                            //
        // ****************************************************************** //

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

        return [$form, $widget, $widget_object, $template_name];
    }

}
