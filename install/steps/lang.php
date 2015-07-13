<?php

function step($langs){

    $result = array(
        'html' => render('step_lang', array(
            'langs' => $langs
        ))
    );

    return $result;

}
