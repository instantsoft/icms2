<?php

function step($is_submit){

    if ($is_submit){
        return check_writables();
    }

    $root = str_replace(DOC_ROOT, '', str_replace(DS, '/', dirname(PATH)));

    $paths = array(
        'root' => $root . '/',
        'upload' => $root . '/' . 'upload' . '/',
        'cache' => $root . '/' . 'cache' . '/'
    );

    $hosts = array(
        'root' => 'http://' . $_SERVER['HTTP_HOST'] . $root,
        'upload' => 'http://' . $_SERVER['HTTP_HOST'] . $root . '/upload',
    );

    $result = array(
        'html' => render('step_paths', array(
            'doc_root' => DOC_ROOT,
            'root' => $root,
            'paths' => $paths,
            'hosts' => $hosts
        ))
    );

    return $result;

}

function check_writables(){

    $error = false;
    $message = '';

    $paths = $_POST['paths'];
    $hosts = $_POST['hosts'];

    $upload = rtrim(DOC_ROOT . $paths['upload'], '/');
    $cache = rtrim(DOC_ROOT . $paths['cache'], '/');

    if (!is_writable($upload)){
        $error = true;
        $message = LANG_PATHS_UPLOAD_PATH . ' '. LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    } else

    if (!is_writable($cache)){
        $error = true;
        $message = LANG_PATHS_CACHE_PATH . ' '. LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    }

    if (!$error){
        $_SESSION['install']['paths'] = $paths;
        $_SESSION['install']['hosts'] = $hosts;
    }

    return array(
        'error' => $error,
        'message' => $message
    );

}
