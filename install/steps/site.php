<?php

function step($is_submit){

    if ($is_submit){
        return check_data();
    }

    $templates = get_templates();

    $site_tpls = [];
    $admin_tpls = [];

    foreach ($templates as $tpl_path => $tpl) {
        if(file_exists($tpl_path .'/main.tpl.php')){
            $site_tpls[$tpl] = $tpl;
        }
        if(file_exists($tpl_path .'/admin.tpl.php')){
            $admin_tpls[$tpl] = $tpl;
        }
    }

    $result = array(
        'html' => render('step_site', array(
            'default_template' => 'modern',
            'default_atemplate' => 'admincoreui',
            'admin_tpls' => $admin_tpls,
            'site_tpls' => $site_tpls
        ))
    );

    return $result;

}

function check_data(){

    $sitename   = trim($_POST['sitename']);
    $hometitle  = trim($_POST['hometitle']);
    $metakeys   = trim($_POST['metakeys']);
    $metadesc   = trim($_POST['metadesc']);
    $template   = trim($_POST['template']);
    $template_admin = trim($_POST['template_admin']);
    $is_check_updates = (int)(isset($_POST['is_check_updates']) ?: 0);

    if (!$sitename){
        return array(
            'error' => true,
            'message' => LANG_SITE_SITENAME_ERROR
        );
    }

    if (!$hometitle){
        return array(
            'error' => true,
            'message' => LANG_SITE_HOMETITLE_ERROR
        );
    }

    $_SESSION['install']['site'] = array(
        'sitename' => $sitename,
        'hometitle' => $hometitle,
        'metakeys' => $metakeys,
        'metadesc' => $metadesc,
        'template' => $template,
        'template_admin' => $template_admin,
        'is_check_updates' => $is_check_updates
    );

    return array(
        'error' => false
    );

}
