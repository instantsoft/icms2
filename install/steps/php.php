<?php

function step($is_submit) {

    $info = check_requirements();

    $result = [
        'html' => render('step_php', [
            'info' => $info
        ])
    ];

    return $result;
}

function check_requirements() {

    $min_php_version  = '7.0.0';
    $extensions       = ['date', 'gd', 'json', 'mbstring', 'mysqli', 'session', 'filter', 'fileinfo'];
    $extensions_extra = ['ftp', 'memcache', 'memcached', 'zip', 'curl'];

    sort($extensions);
    sort($extensions_extra);

    $info = [];

    $info['valid'] = true;

    $info['php'] = [
        'version' => implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]),
        'valid'   => (version_compare(PHP_VERSION, $min_php_version) >= 0)
    ];

    $info['valid'] = $info['valid'] && $info['php']['valid'];

    foreach ($extensions as $ext) {
        $loaded            = extension_loaded($ext);
        $info['ext'][$ext] = $loaded;
        $info['valid']     = $info['valid'] && $loaded;
    }

    foreach ($extensions_extra as $ext) {
        $info['ext_extra'][$ext] = extension_loaded($ext);
    }

    return $info;
}
