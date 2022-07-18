<?php

function step($is_submit) {

    if ($is_submit) {
        return check_writables();
    }

    $root = str_replace(DOC_ROOT, '', str_replace(DS, '/', dirname(PATH)));

    $sp = session_save_path();

    if (!$sp) {
        $sp = sys_get_temp_dir();
    }

    $sp = rtrim($sp, '/');

    $uniq = uniqid();

    if (mkdir($sp . DS . $uniq, 0755, true) && is_writable($sp . DS . $uniq)) {
        $sp .= DS . $uniq;
    }

    $paths = [
        'session_save_path' => $sp,
        'root'              => $root . '/',
        'upload'            => $root . '/' . 'upload' . '/',
        'cache'             => $root . '/' . 'cache' . '/'
    ];

    $protocol = 'http://';
    if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
        $protocol = 'https://';
    }

    $hosts = [
        'root'   => $protocol . rtrim($_SERVER['HTTP_HOST'], '/') . $root,
        'upload' => $protocol . rtrim($_SERVER['HTTP_HOST'], '/') . $root . '/upload',
    ];

    $open_basedir      = @ini_get('open_basedir');
    $open_basedir_hint = '';

    if ($open_basedir) {
        $open_basedirs     = explode(PATH_SEPARATOR, $open_basedir);
        $open_basedir_hint = LANG_PATHS_SESSIONS_BASEDIR . implode(', ', $open_basedirs);
    }

    return [
        'html' => render('step_paths', [
            'doc_root'          => DOC_ROOT,
            'open_basedir_hint' => $open_basedir_hint,
            'root'              => $root,
            'paths'             => $paths,
            'hosts'             => $hosts
        ])
    ];
}

function check_writables() {

    $error   = false;
    $message = '';

    $paths = get_post_array('paths');
    $hosts = get_post_array('hosts');

    $hosts['root']   = rtrim($hosts['root'], '/');
    $hosts['upload'] = rtrim($hosts['upload'], '/');

    $upload = rtrim(DOC_ROOT . $paths['upload'], '/');
    $cache  = rtrim(DOC_ROOT . $paths['cache'], '/');

    if (empty($paths['upload']) || empty($paths['cache']) ||
            empty($paths['session_save_path']) || empty($hosts['root']) ||
            empty($hosts['upload']) || empty($paths['root'])) {
        return [
            'error'   => true,
            'message' => LANG_ADMIN_ERROR
        ];
    }

    if (!is_writable($upload)) {

        $error   = true;
        $message = LANG_PATHS_UPLOAD_PATH . ' ' . LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    } else if (!is_writable($cache)) {

        $error   = true;
        $message = LANG_PATHS_CACHE_PATH . ' ' . LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    } else if (!is_writable($paths['session_save_path'])) {

        $error   = true;
        $message = LANG_PATHS_SESSION_PATH . ' ' . LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    }

    if (!$error) {
        $_SESSION['install']['paths'] = $paths;
        $_SESSION['install']['hosts'] = $hosts;
    }

    return [
        'error'   => $error,
        'message' => $message
    ];
}
