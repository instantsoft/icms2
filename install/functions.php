<?php

function is_ajax_request(){
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){ return false; }
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

function render($template_name, $data=array()){
    extract($data);
    ob_start();
    include PATH . "templates/{$template_name}.php";
    return ob_get_clean();
}

function run_step($step, $is_submit=false){
    require PATH . "steps/{$step['id']}.php";
    $result = step($is_submit);
    return $result;
}

function is_config_exists() {
    return is_readable(dirname(PATH).DS.'system/config/config.php');
}

function get_site_config() {

    static $cfg = null;

    if(isset($cfg)){ return $cfg; }

    $cfg_file = dirname(PATH).DS.'system/config/config.php';

    if(!is_readable($cfg_file)){
        return false;
    }

    $cfg = include $cfg_file;

    return $cfg;

}

function is_db_connected() {

    $cfg = get_site_config();

    if($cfg){

        $mysqli = @new mysqli($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_base']);

        if (!$mysqli->connect_error) {
            return true;
        }

    }

    return false;

}

function get_db_list() {

    $cfg = get_site_config();

    if($cfg){

        $mysqli = @new mysqli($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_base']);

        if (!$mysqli->connect_error) {

            $r = $mysqli->query('SHOW DATABASES');
            if (!$r) { return false; }

            $list = array();

            while($data = $r->fetch_assoc()){
                if(in_array($data['Database'], array('information_schema', 'mysql', 'performance_schema', 'phpmyadmin'))){
                    continue;
                }
                $list[$data['Database']] = $data['Database'];
            }

            return $list;

        }

    }

    return false;

}
function get_version($show_date = false){

    $file = dirname(PATH).DS.'system/config/version.ini';

    if (!is_readable($file)){ return ''; }

    $version = parse_ini_file($file);

    if (!$show_date && isset($version['date'])) { unset($version['date']); }

    return implode('.', $version);

}

function make_json($array){

    $json = '{';
    $pairs = array();

    foreach($array as $key=>$val){
        if (!is_numeric($val)) { $val = "'{$val}'"; }
        $pairs[] = "{$key}: $val";
    }

    $json .= implode(', ', $pairs);
    $json .= '}';

    return $json;

}

function html_bool_span($value, $condition){
    if ($condition){
        return '<span class="positive">' . $value . '</span>';
    } else {
        return '<span class="negative">' . $value . '</span>';
    }
}

function get_langs(){

    $dir = PATH . 'languages';
    $dir_context = opendir($dir);

    $list = array();

    while ($next = readdir($dir_context)){

        if (in_array($next, array('.', '..'))){ continue; }
        if (strpos($next, '.') === 0){ continue; }
        if (!is_dir($dir.'/'.$next)) { continue; }

        $list[] = $next;

    }

    return $list;

}

function copy_folder($dir_source, $dir_target) {

    if (is_dir($dir_source))  {

        @mkdir($dir_target);
        $d = dir($dir_source);

        while (false !== ($entry = $d->read())) {
            if ($entry == '.' || $entry == '..') { continue; }
            copy_folder("$dir_source/$entry", "$dir_target/$entry");
        }

        $d->close();

    } else {
        copy($dir_source, $dir_target);
    }

}