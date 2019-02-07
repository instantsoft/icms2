<?php

function step($is_submit=false){

    $host = $_SESSION['install']['hosts']['root'];

    unset($_SESSION['install']);

    $result = array(
        'html' => render('step_finish', array(
            'host' => $host
        ))
    );

    return $result;

}
