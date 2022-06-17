<?php

/**
 * Returns months names for current language
 * @return array
 */
function lang_months() {
    return [
        'January', 'February', 'March', 'April', 'May', 'June', 'July',
        'August', 'September', 'October', 'November', 'December'
    ];
}

/**
 * Returns days names for current language
 * @return array
 */
function lang_days() {
    return [
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
    ];
}

/**
 * Returns date for current language
 * @param string $date_string
 * @return string
 */
function lang_date($date_string) {

    $eng_months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    return str_replace($eng_months, lang_months(), $date_string);
}

/**
 * Converts string from current language to SLUG
 * @param string $string Input string
 * @param boolean $disallow_numeric Disallow numeric SLUG
 * @return string
 */
function lang_slug($string, $disallow_numeric = true) {

    $string = strip_tags(trim($string));
    $string = mb_strtolower($string);
    $string = str_replace(' ', '-', $string);

    $slug = preg_replace('/[^a-z0-9\-\/]/u', '-', $string);
    $slug = preg_replace('/([-]+)/i', '-', $slug);
    $slug = trim($slug, '-');

    if (!$slug) {
        $slug = 'untitled';
    }
    if ($disallow_numeric && is_numeric($slug)) {
        $slug .= strtolower(date('F'));
    }

    return $slug;
}

/**
 * Set locale information
 * @return mixed
 */
function lang_setlocale() {

    $locale = 'en_US.UTF-8';

    setlocale(LC_COLLATE, $locale);
    setlocale(LC_CTYPE, $locale);
    setlocale(LC_MONETARY, $locale);
    setlocale(LC_TIME, $locale);
    if (defined('LC_MESSAGES')) {
        setlocale(LC_MESSAGES, $locale);
    }
    setlocale(LC_NUMERIC, 'POSIX');

    return true;
}
