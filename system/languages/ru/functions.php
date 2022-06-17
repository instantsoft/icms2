<?php

/**
 * Returns months names for current language
 * @return array
 */
function lang_months() {
    return [
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ];
}

/**
 * Returns days names for current language
 * @return array
 */
function lang_days() {
    return [
        'вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'
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

    $slug = preg_replace('/[^a-zа-яё0-9\-\/]/u', '-', $string);
    $slug = preg_replace('/([-]+)/i', '-', $slug);
    $slug = trim($slug, '-');

    $ru_en = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y',
        'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ja'
    ];

    $slug = strtr($slug, $ru_en);

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

    $locale = 'ru_RU.UTF-8';

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

/**
 * Locale name
 */
define('LC_LANGUAGE_TERRITORY', 'ru_RU');

/**
 * Locale validate regexp
 */
define('LC_LANGUAGE_VALIDATE_REGEXP', "/^([a-zа-яёй0-9 \.\?\@\,\-]*)$/ui");
