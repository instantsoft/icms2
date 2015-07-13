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

    $sitename   = $_POST['sitename'];
    $hometitle  = $_POST['hometitle'];
    $metakeys   = $_POST['metakeys'];
    $metadesc   = $_POST['metadesc'];

    if (!$sitename){
        return array(
            'error' => true,
            'message' => LANG_SITE_SITENAME_ERROR
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
