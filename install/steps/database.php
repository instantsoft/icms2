<?php

function step($is_submit) {

    if ($is_submit) {
        return check_db();
    }

    $db_list = get_db_list();

    $cfg = get_site_config();

    $result = [
        'html' => render('step_database', [
            'cfg'     => $cfg,
            'db_list' => $db_list
        ])
    ];

    return $result;
}

function get_db_collation($charset) {
    $collations = [
        'utf8'    => 'utf8_general_ci',
        'utf8mb3' => 'utf8mb3_general_ci',
        'utf8mb4' => 'utf8mb4_general_ci'
    ];
    return isset($collations[$charset]) ? $collations[$charset] : false;
}

function check_db() {

    $start_time = microtime(true);

    $db = get_post_array('db');

    $db['host']             = trim($db['host']);
    $db['user']             = trim($db['user']);
    $db['base']             = trim($db['base']);
    $db['engine']           = trim($db['engine']);
    $db['db_charset']       = trim($db['db_charset']);
    $db['clear_sql_mode']   = 0;
    $db['innodb_full_text'] = 0;

    if (!preg_match('#^[a-z0-9\_]+$#i', $db['prefix'])) {
        return [
            'error'   => true,
            'message' => LANG_DATABASE_PREFIX_ERROR
        ];
    }

    mysqli_report(MYSQLI_REPORT_STRICT);

    try {

        $mysqli = new mysqli($db['host'], $db['user'], $db['pass']);
    } catch (Exception $e) {

        return [
            'error'   => true,
            'message' => sprintf(LANG_DATABASE_CONNECT_ERROR, $e->getMessage())
        ];
    }

    $result = $mysqli->query('select version() as `version`;');
    if($result){

        $vdata = $result->fetch_assoc();
        $result->close();

        if(!empty($vdata['version'])){

            if(version_compare($vdata['version'], '5.7') >= 0){
                $db['clear_sql_mode'] = 1;
            }

            // Полнотекстовый поиск InnoDB для MariaDB
            if (stripos($vdata['version'], 'MariaDB') !== false) {

                if (version_compare($vdata['version'], '10.0.5') >= 0) {
                    $db['innodb_full_text'] = 1;
                }

            } else {

                // Полнотекстовый поиск InnoDB для MySQL
                if(version_compare($vdata['version'], '5.6.4') >= 0){
                    $db['innodb_full_text'] = 1;
                }
            }
        }
    }

    $check_engine = check_db_engine($mysqli, $db['engine']);

    if ($check_engine !== true) {
        return [
            'error'   => true,
            'message' => $check_engine
        ];
    }

    $check_charset = check_db_charset($mysqli, $db['db_charset']);

    if ($check_charset !== true) {
        return [
            'error'   => true,
            'message' => $check_charset
        ];
    }

    $mysqli->set_charset($db['db_charset']);
    $mysqli->query("SET sql_mode=''");

    $collation_name = get_db_collation($db['db_charset']);

    if (!empty($db['create_db'])) {

        $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$db['base']}` DEFAULT CHARACTER SET {$db['db_charset']} COLLATE {$collation_name}");
    }

    $mysqli->query("ALTER DATABASE {$db['base']} CHARACTER SET {$db['db_charset']} COLLATE {$collation_name}");

    if (!$mysqli->select_db($db['base'])) {
        return [
            'error'   => true,
            'message' => sprintf(LANG_DATABASE_SELECT_ERROR, $db['base'])
        ];
    }

    // Список дампов для импортирования
    $dumps = get_sql_file_names(!empty($db['is_install_demo_content']));

    $success = true;

    // Основные дампы
    foreach ($dumps as $dump_name) {
        if ($success === true) {
            $success = import_dump(
                $mysqli,
                $dump_name,
                $db['prefix'],
                $db['engine'],
                ';',
                $db['db_charset'],
                $db['innodb_full_text']
            );
        }
    }

    // Импортируем дампы отдельных компонентов
    $packages_list = get_packages_sql_list();

    foreach ($packages_list as $controller_name) {
        foreach ($dumps as $dump_name) {
            if ($success === true) {
                $success = import_dump(
                    $mysqli,
                    'packages' . DS . $controller_name . DS . $dump_name,
                    $db['prefix'],
                    $db['engine'],
                    ';',
                    $db['db_charset'],
                    $db['innodb_full_text']
                );
            }
        }
    }

    // Теперь импортируем дампы по зависимостям контроллеров
    $result = $mysqli->query("SELECT `name` FROM `{$db['prefix']}controllers`");
    $controllers = [];
    if (!$mysqli->errno) {
        while ($item = $result->fetch_assoc()) {
            $controllers[] = $item['name'];
        }
        $result->close();
    }

    foreach ($packages_list as $controller_name) {
        foreach ($controllers as $rel_controller_name) {
            foreach ($dumps as $dump_name) {
                if ($success === true) {
                    $success = import_dump(
                        $mysqli,
                        'packages' . DS . $controller_name . DS . $rel_controller_name . DS. $dump_name,
                        $db['prefix'],
                        $db['engine'],
                        ';',
                        $db['db_charset'],
                        $db['innodb_full_text']
                    );
                }
            }
        }
    }

    if ($success === true) {

        $dir_install_upload = PATH . DS . 'upload' . DS . $_SESSION['install']['site']['template'];
        $dir_upload         = dirname(PATH) . DS . 'upload';
        copy_folder($dir_install_upload, $dir_upload);

        if (!$db['users_exists']) {
            $db['users_table'] = $db['prefix'] . 'users';
        }
        $_SESSION['install']['db'] = $db;
    }

    return [
        'time'    => number_format((microtime(true) - $start_time), 3),
        'error'   => $success !== true,
        'message' => is_string($success) ? $success : LANG_DATABASE_BASE_ERROR
    ];
}

function check_db_engine($mysqli, $engine) {

    $r = $mysqli->query('SHOW ENGINES');
    if ($r === false) {
        return true; // невозможно выполнить запрос, оставляем на откуп пользователю
    }

    while ($data = $r->fetch_assoc()) {
        if ($data['Engine'] == $engine) {
            if (in_array($data['Support'], ['YES', 'DEFAULT'], true)) {
                return true;
            } else {
                return constant('LANG_DATABASE_ENGINE_' . $data['Support']);
            }
        }
    }

    return LANG_DATABASE_ENGINE_ERROR;
}

function check_db_charset($mysqli, $charset) {

    $charset_name = get_db_collation($charset);

    if (!$charset_name) {
        return LANG_DATABASE_CH_ERROR;
    }

    $r = $mysqli->query("SELECT 1 FROM `information_schema`.`COLLATIONS` WHERE `COLLATION_NAME` = '{$charset_name}' AND `CHARACTER_SET_NAME` = '{$charset}' AND `IS_COMPILED` = 'Yes'");
    if ($r === false) {
        return true; // невозможно выполнить запрос, оставляем на откуп пользователю
    }

    if (!$r->num_rows) {
        return LANG_DATABASE_CH_ERROR;
    }

    return true;
}

function get_sql_file_names($is_install_demo_content = false) {

    $dumps = [
        // Основной дамп
        'base.sql',
        // Базовые виджеты для шаблона
        'widgets_bind_' . $_SESSION['install']['site']['template'] . '.sql',
    ];

    if ($is_install_demo_content) {
        // Демо данные
        $dumps[] = 'base_demo_' . $_SESSION['install']['site']['template'] . '.sql';
        // Демо виджеты для шаблона
        $dumps[] = 'widgets_bind_demo_' . $_SESSION['install']['site']['template'] . '.sql';
    }

    return $dumps;
}

function import_dump(&$mysqli, $file, $prefix, $engine = 'MyISAM', $delimiter = ';', $charset = 'utf8', $innodb_full_text = 0) {

    clearstatcache();

    @set_time_limit(0);

    $file = PATH . 'languages' . DS . LANG . DS . 'sql' . DS . $file;

    // Кастомные SQL могут отсутствовать
    if (!file_exists($file)) {
        return true;
    }

    if (!is_readable($file)) {
        return false;
    }

    $read_file_dump_query = function ($filename) use($prefix, $engine, $delimiter, $charset, $innodb_full_text) {

        $file = fopen($filename, 'r');

        $buffer = '';

        while (($line = fgets($file)) !== false) {

            $buffer .= $line;

            if (substr(rtrim($line), -1) === $delimiter) {

                $query = rtrim($buffer);

                $buffer = '';

                $query = str_replace(['{#}', 'InnoDB', 'CHARSET=utf8'], [$prefix, $engine, 'CHARSET=' . $charset], $query);

                if ($innodb_full_text && $engine === 'InnoDB') {
                    $query = str_replace('MyISAM', 'InnoDB', $query);
                }

                yield $query;
            }
        }

        fclose($file);
    };

    $success = false;

    foreach ($read_file_dump_query($file) as $query) {

        $mysqli->query($query);

        if ($mysqli->errno) {
            return $mysqli->error . "\n\n" . $query;
        }

        $success = true;
    }

    return $success;
}
