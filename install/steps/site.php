<?php

function step($is_submit) {

    if ($is_submit) {
        return check_data();
    }

    $templates = get_templates();

    $site_tpls  = [];
    $admin_tpls = [];

    foreach ($templates as $tpl_path => $tpl) {
        if (file_exists($tpl_path . '/main.tpl.php')) {
            $site_tpls[$tpl] = $tpl;
        }
        if (file_exists($tpl_path . '/admin.tpl.php')) {
            $admin_tpls[$tpl] = $tpl;
        }
    }

    return [
        'html' => render('step_site', [
            'default_template'  => 'modern',
            'default_atemplate' => 'admincoreui',
            'admin_tpls'        => $admin_tpls,
            'site_tpls'         => $site_tpls
        ])
    ];
}

function check_data() {

    $sitename         = strip_tags(get_post('sitename'));
    $hometitle        = strip_tags(get_post('hometitle'));
    $metakeys         = strip_tags(get_post('metakeys'));
    $metadesc         = strip_tags(get_post('metadesc'));
    $template         = strip_tags(get_post('template'));
    $template_admin   = strip_tags(get_post('template_admin'));
    $is_check_updates = (int) get_post('is_check_updates');

    if (!$sitename) {
        return [
            'error'   => true,
            'message' => LANG_SITE_SITENAME_ERROR
        ];
    }

    if (!$hometitle) {
        return [
            'error'   => true,
            'message' => LANG_SITE_HOMETITLE_ERROR
        ];
    }

    $_SESSION['install']['site'] = [
        'sitename'         => $sitename,
        'hometitle'        => $hometitle,
        'metakeys'         => $metakeys,
        'metadesc'         => $metadesc,
        'template'         => $template,
        'template_admin'   => $template_admin,
        'is_check_updates' => $is_check_updates
    ];

    return [
        'error' => false
    ];
}
