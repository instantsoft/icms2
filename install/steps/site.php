<?php

function step($is_submit){

    if ($is_submit){
        return check_data();
    }

    $result = array(
        'html' => render('step_site', array(
        ))
    );

    return $result;

}

function check_data(){

    $sitename   = trim($_POST['sitename']);
    $hometitle  = trim($_POST['hometitle']);
    $metakeys   = trim($_POST['metakeys']);
    $metadesc   = trim($_POST['metadesc']);

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
    );

    return array(
        'error' => false,
    );

}
