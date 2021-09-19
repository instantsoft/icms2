<?php

if (version_compare(PHP_VERSION, '5.6') < 0) {
    return false;
}

if (! class_exists('ScssPhp\ScssPhp\Version')) {
    spl_autoload_register(function ($class) {
        if (0 !== strpos($class, 'ScssPhp\ScssPhp\\')) {
            // Not a ScssPhp class
            return;
        }

        $subClass = substr($class, strlen('ScssPhp\ScssPhp\\'));
        $path = __DIR__ . '/' . str_replace('\\', '/', $subClass) . '.php';

        if (file_exists($path)) {
            require $path;
        }
    });
}

return true;
