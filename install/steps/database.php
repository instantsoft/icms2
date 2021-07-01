<?php

function step($is_submit){

    if ($is_submit){
        return check_db();
    }

    $db_list = get_db_list();

    $cfg = get_site_config();

    $result = array(
        'html' => render('step_database', array(
            'cfg'     => $cfg,
            'db_list' => $db_list
        ))
    );

    return $result;

}

function get_db_collation($charset) {
    $collations = [
        'utf8' => 'utf8_general_ci',
        'utf8mb4' => 'utf8mb4_general_ci'
    ];
    return isset($collations[$charset]) ? $collations[$charset] : false;
}

function check_db(){

    $db = $_POST['db'];

    $db['host']   = trim($db['host']);
    $db['user']   = trim($db['user']);
    $db['base']   = trim($db['base']);
    $db['engine'] = trim($db['engine']);
    $db['db_charset'] = trim($db['db_charset']);
    $db['clear_sql_mode'] = 0;

    if (!preg_match('#^[a-z0-9\_]+$#i', $db['prefix'])) {
        return array(
            'error'   => true,
            'message' => LANG_DATABASE_PREFIX_ERROR
        );
    }

    mysqli_report(MYSQLI_REPORT_STRICT);

    try {

        $mysqli = new mysqli($db['host'], $db['user'], $db['pass']);

    } catch (Exception $e) {

        return array(
            'error'   => true,
            'message' => sprintf(LANG_DATABASE_CONNECT_ERROR, $e->getMessage())
        );

    }

    if(!empty($mysqli->server_info)){
        if(version_compare($mysqli->server_info, '5.7') >= 0){
            $db['clear_sql_mode'] = 1;
        }
    }

    $check_engine = check_db_engine($mysqli, $db['engine']);

    if ($check_engine !== true) {
        return array(
            'error' => true,
            'message' => $check_engine
        );
    }

    $check_charset = check_db_charset($mysqli, $db['db_charset']);

    if ($check_charset !== true) {
        return array(
            'error' => true,
            'message' => $check_charset
        );
    }

    $mysqli->set_charset($db['db_charset']);
    $mysqli->query("SET sql_mode=''");

    $collation_name = get_db_collation($db['db_charset']);

    if (!empty($db['create_db'])) {

        $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$db['base']}` DEFAULT CHARACTER SET {$db['db_charset']} COLLATE {$collation_name}");

    }

    $mysqli->query("ALTER DATABASE {$db['base']} CHARACTER SET {$db['db_charset']} COLLATE {$collation_name}");

    if (!$mysqli->select_db($db['base'])) {
        return array(
            'error' => true,
            'message' => sprintf(LANG_DATABASE_SELECT_ERROR, $db['base'])
        );
    }

    // Для innodb оборачиваем в транзакцию, это быстрее и удобней
    if($db['engine'] == 'InnoDB'){
        $mysqli->autocommit(false);
    }

    // Основной дамп
    $success = import_dump($mysqli, 'base.sql', $db['prefix'], $db['engine'], ';', $db['db_charset']);
    // Гео
    if($success === true){
        $success = import_dump($mysqli, 'geo.sql', $db['prefix'], $db['engine'], ';', $db['db_charset']);
    }
    // Виджеты для шаблона
    if($success === true){
        $success = import_dump($mysqli, 'widgets_bind_'.$_SESSION['install']['site']['template'].'.sql', $db['prefix'], $db['engine'], ';', $db['db_charset']);
    }

    // Демо данные
    if ($success === true && !empty($db['is_install_demo_content'])) {
        $success = import_dump($mysqli, 'base_demo_content.sql', $db['prefix'], $db['engine'], ';', $db['db_charset']);
        // Демо виджеты для шаблона
        if($success === true){
            $success = import_dump($mysqli, 'widgets_bind_demo_'.$_SESSION['install']['site']['template'].'.sql', $db['prefix'], $db['engine'], ';', $db['db_charset']);
        }
    }

    if($db['engine'] == 'InnoDB'){
        if ($success === true) {
            $mysqli->commit();
        } else {
            $mysqli->rollback();
        }
        $mysqli->autocommit(true);
    }

    if ($success === true){

        $dir_install_upload = PATH . DS . 'upload';
        $dir_upload = dirname(PATH) . DS . 'upload';
        copy_folder($dir_install_upload, $dir_upload);

        if (!$db['users_exists']){
            $db['users_table'] = $db['prefix'] . 'users';
        }
        $_SESSION['install']['db'] = $db;
    }

    return array(
        'error' => $success !== true,
        'message' => is_string($success) ? $success : LANG_DATABASE_BASE_ERROR
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

function check_db_charset($mysqli, $charset){

    $charset_name = get_db_collation($charset);

    if(!$charset_name){
        return LANG_DATABASE_CH_ERROR;
    }

    $r = $mysqli->query("SELECT 1 FROM `information_schema`.`COLLATIONS` WHERE `COLLATION_NAME` = '{$charset_name}' AND `CHARACTER_SET_NAME` = '{$charset}' AND `IS_COMPILED` = 'Yes'");
    if ($r === false) {
        return true; // невозможно выполнить запрос, оставляем на откуп пользователю
    }

    if(!$r->num_rows){
        return LANG_DATABASE_CH_ERROR;
    }

    return true;

}

function import_dump(&$mysqli, $file, $prefix, $engine='MyISAM', $delimiter = ';', $charset = 'utf8'){

    clearstatcache();
    @set_time_limit(0);

    $file = PATH . 'languages' . DS . LANG . DS . 'sql' . DS . $file;

    // Кастомные SQL могут отсутствовать
    if (!file_exists($file)){ return true; }

    if (!is_readable($file)){ return false; }

    $file = fopen($file, 'r');

    $query = []; $success = false;

    while (feof($file) === false){

        $query[] = fgets($file);

        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1){

            $success = true;

            $query = trim(implode('', $query));

            $query = str_replace(array('{#}', 'InnoDB', 'CHARSET=utf8'), array($prefix, $engine, 'CHARSET='.$charset), $query);

            $result = $mysqli->query($query);

            if ($mysqli->errno) {
                return $mysqli->error."\n\n".$query;
            }

        }

        if (is_string($query) === true){
            $query = array();
        }

    }

    fclose($file);

    return $success;

}
