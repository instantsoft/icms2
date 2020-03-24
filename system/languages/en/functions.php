<?php

/**
 * Returns months names for current language
 * @return array
 */
function lang_months(){
    return array(
        'January', 'February', 'March', 'April', 'May', 'June', 'July',
        'August', 'September', 'October', 'November', 'December'
    );
}

/**
 * Returns days names for current language
 * @return array
 */
function lang_days(){
    return array(
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
    );
}

function lang_date($date_string){

    $eng_months = array(
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    );

    $date_string = str_replace($eng_months, lang_months(), $date_string);

    return $date_string;

}

/**
 * Converts string from current language to SLUG
 * @param string $string Input string
 * @param boolean $disallow_numeric Disallow numeric SLUG
 * @return string
 */
function lang_slug($string, $disallow_numeric = true){

    $string    = strip_tags(trim($string));
    $string    = mb_strtolower($string, 'utf-8');
    $string    = str_replace(' ', '-', $string);

    $slug = preg_replace ('/[^a-z0-9\-\/]/u', '-', $string);
    $slug = preg_replace('/([-]+)/i', '-', $slug);
    $slug = trim($slug, '-');

    if (!$slug){ $slug = 'untitled'; }
    if ($disallow_numeric && is_numeric($slug)){ $slug .= strtolower(date('F')); }

    return $slug;

}

/**
 * Set locale information
 * @return mixed
 */
function lang_setlocale() {
    setlocale(LC_ALL, 'en_US.UTF-8');
    setlocale(LC_NUMERIC, 'POSIX');
    return true;
}
