<?php

/**
 * Определяет и подключает PHP-файл содержащий указанный класс
 * @param string $_class_name
 * @return boolean
 */
function autoLoadCoreClass($_class_name){

    $class_name = strtolower($_class_name);
    $class_file = false;

    if (strpos($class_name, 'cms') === 0) {
        $class_name = substr($class_name, 3);
        $class_file = 'system/core/' . $class_name . '.php';
    } else

    if (strpos($class_name, 'field') === 0) {
        $class_name = substr($class_name, 5);
        $class_file = 'system/fields/' . $class_name . '.php';
    } else

    if (strpos($class_name, 'model') === 0) {
        $cut_num = 5; $path = '';
        if (strpos($class_name, 'modelbackend') === 0) {
            $cut_num = 12; $path = '/backend';
        }
        $controller = strtolower(
            preg_replace(
                ['/([A-Z]+)/', '/_([A-Z]+)([A-Z][a-z])/'],
                ['_$1', '_$1_$2'],
                lcfirst(substr($_class_name, $cut_num))
            )
        );
        $class_file = 'system/controllers/' . $controller . $path . '/model.php';

    } else

    if (strpos($class_name, 'icms\\') === 0) {

        $paths = explode('\\', $_class_name);

        // Удаляем префикс icms
        // он для отделения namespace-ов от других
        unset($paths[0]);

        $class_file = 'system/' . implode('/', $paths) . '.php';
    }

    if (!$class_file){ return false; }

    if (!is_readable(PATH . '/' . $class_file)){
        throw new Exception($class_file);
    }

    include_once PATH . '/' . $class_file;

    return true;
}
