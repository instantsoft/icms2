<?php

/**
 * Определяет и подключает PHP-файл содержащий указанный класс
 * @param string $class_name
 * @return boolean
 */
function autoLoadCoreClass($class_name){

    $class_name = mb_strtolower($class_name);
    $class_file = false;

    if ( mb_substr($class_name, 0, 3) == 'cms' ) {
        $class_name = mb_substr($class_name, 3);
        $class_file = 'system/core/' . $class_name . '.php';
    }

    if ( mb_substr($class_name, 0, 5) == 'field' ) {
        $class_name = mb_substr($class_name, 5);
        $class_file = 'system/fields/' . $class_name . '.php';
    }

    if (!$class_file){ return false; }

    include_once PATH . '/' . $class_file;

    return true;

}
