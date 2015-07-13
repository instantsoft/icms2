<?php

function step($is_submit){

    $result = array(
        'html' => render('step_start', array(
               'langs' => get_langs(),
               'lang' => LANG,
        ))
    );

    return $result;

}
