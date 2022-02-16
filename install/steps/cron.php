<?php

function step($is_submit) {

    $doc_root = DOC_ROOT . str_replace(DOC_ROOT, '', str_replace(DS, '/', dirname(PATH)));

    return [
        'html' => render('step_cron', [
            'doc_root' => $doc_root,
            'php_path' => get_program_path('php')
        ])
    ];
}
