<?php

function step($is_submit = false) {

    $host = $_SESSION['install']['hosts']['root'];

    unset($_SESSION['install']);

    return [
        'html' => render('step_finish', [
            'host' => $host
        ])
    ];
}
