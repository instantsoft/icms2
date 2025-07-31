<?php

function step($is_submit) {

    $addons_dirs = get_dirs_list(PATH . 'externals', true);

    return [
        'html' => render('step_addons', [
            'addons' => addons_dirs_to_data($addons_dirs)
        ])
    ];
}

function addons_dirs_to_data($addons_dirs) {

    $res = [];

    foreach ($addons_dirs as $dir_name) {

        $package_path = PATH . 'externals/' . $dir_name;

        $ini_file         = $package_path . '/manifest.'.LANG.'.ini';
        $ini_file_default = $package_path . '/manifest.ru.ini';

        if (!is_readable($ini_file)) {
            $ini_file = $ini_file_default;
        }

        if (!is_readable($ini_file)) {
            continue;
        }

        $manifest = parse_ini_file($ini_file, true);

        $res[$dir_name] = [
            'title'   => $manifest['info']['title'],
            'author'  => $manifest['author']['name'] ?? '',
            'url'     => $manifest['author']['url'] ?? null,
            'version' => $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build']
        ];
    }

    return $res;
}
