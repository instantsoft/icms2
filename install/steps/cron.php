<?php

function step($is_submit){

    $doc_root = str_replace(DS, '/', $_SERVER['DOCUMENT_ROOT']);

    $result = array(
        'html' => render('step_cron', array(
            'doc_root' => $doc_root
        ))
    );

    return $result;

}
