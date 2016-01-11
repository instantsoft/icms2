<?php

function step($is_submit){

    if ($is_submit){
        return check_db();
    }

    $result = array(
        'html' => render('step_database', array(
        ))
    );

    return $result;

}

function check_db(){

    $db = $_POST['db'];

    $db['host']   = trim($db['host']);
    $db['user']   = trim($db['user']);
    $db['base']   = trim($db['base']);
    $db['engine'] = trim($db['engine']);

    $mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);

    if ($mysqli->connect_error) {
        return array(
            'error' => true,
            'message' => sprintf(LANG_DATABASE_CONNECT_ERROR, $mysqli->connect_error)
        );
    }

    $mysqli->set_charset("utf8");

    $check_engine = check_db_engine($mysqli, $db['engine']);

    if ($check_engine !== true) {
        return array(
            'error' => true,
            'message' => $check_engine
        );
    }

    $success = import_dump($mysqli, 'base.sql', $db['prefix'], $db['engine']);
    if($success){
        $success = import_dump($mysqli, 'geo.sql', $db['prefix'], $db['engine']);
    }

    if ($success && !empty($db['is_install_demo_content'])) {
        $success = import_dump($mysqli, 'base_demo_content.sql', $db['prefix'], $db['engine']);
        $dir_install_upload = PATH . DS . 'upload';
        $dir_upload = DOC_ROOT . DS . 'upload';
        copy_folder($dir_install_upload, $dir_upload);
    }

    if ($success){
        if (!$db['users_exists']){
            $db['users_table'] = $db['prefix'] . 'users';
        }
        $_SESSION['install']['db'] = $db;
    }

    return array(
        'error' => !$success,
        'message' => LANG_DATABASE_BASE_ERROR
    );

}

function check_db_engine($mysqli, $engine){

    $r = $mysqli->query('SHOW ENGINES');
    if ($r === false) {
        return true; // невозможно выполнить запрос, оставляем на откуп пользователю
    }

    while($data = $r->fetch_assoc()){
        if($data['Engine'] == $engine){
            if(in_array($data['Support'], array('YES', 'DEFAULT'), true)){
                return true;
            } else {
                return constant('LANG_DATABASE_ENGINE_'.$data['Support']);
            }
        }
    }

    return LANG_DATABASE_ENGINE_ERROR;

}

function import_dump($mysqli, $file, $prefix, $engine='MyISAM', $delimiter = ';'){

    @set_time_limit(0);

    $file = PATH . 'languages' . DS . LANG . DS . $file;

    if (!is_file($file)){ return false; }

    $file = fopen($file, 'r');

    $query = array();

    while (feof($file) === false){

        $query[] = fgets($file);

        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1){

            $query = trim(implode('', $query));

            $query = str_replace(array('{#}', 'InnoDB'), array($prefix, $engine), $query);

            $result = $mysqli->query($query);

            if ($result === false) {
                return false;
            }

        }

        if (is_string($query) === true){
            $query = array();
        }

    }

    fclose($file);

    return true;

}