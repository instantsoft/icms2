<?php

function step($is_submit) {

    return [
        'html' => render('step_start', [
            'langs' => get_langs(),
            'lang'  => LANG
        ])
    ];
}
