<?php

function step($is_submit=false){

    $host = $_SESSION['install']['hosts']['root'];

    unset($_SESSION['install']);

    $_SESSION['user']['id'] = 1;

    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];

    $octets = explode('.', $_SERVER['REMOTE_ADDR']);
    $end_okets = end($octets);

    $_SESSION['user_net'] = rtrim($_SERVER['REMOTE_ADDR'], $end_okets);

    $result = array(
        'html' => render('step_finish', array(
            'host' => $host
        ))
    );

    return $result;

}
