<?php

spl_autoload_register(function ($class) {
    if (0 !== strpos($class, 'PHPMailer\PHPMailer\\')) {
        return;
    }

    $subClass = substr($class, strlen('PHPMailer\PHPMailer\\'));
    $path = __DIR__ . '/' . str_replace('\\', '/', $subClass) . '.php';

    if (file_exists($path)) {
        require $path;
    }
});
