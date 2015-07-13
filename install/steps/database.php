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

    $db['host'] = trim($db['host']);
    $db['user'] = trim($db['user']);
    $db['base'] = trim($db['base']);

    $mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);

    if ($mysqli->connect_error) {
        return array(
            'error' => true,
            'message' => sprintf(LANG_DATABASE_CONNECT_ERROR, $mysqli->connect_error)
        );
    }

    $mysqli->set_charset("utf8");

    $success = import_dump($mysqli, 'base.sql', $db['prefix']);

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

function import_dump($mysqli, $file, $prefix, $delimiter = ';'){

    set_time_limit(0);

    $file = PATH . 'languages' . DS . LANG . DS . $file;

    if (!is_file($file)){ return false; }

    $file = fopen($file, 'r');

    $query = array();

    while (feof($file) === false){

        $query[] = fgets($file);

        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1){

            $query = trim(implode('', $query));

            $query = str_replace('{#}', $prefix, $query);

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
