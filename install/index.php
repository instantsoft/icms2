<?php

session_start();

define('DS', DIRECTORY_SEPARATOR);
define('PATH', dirname(__FILE__).DS);
define('DOC_ROOT', str_replace(DS, '/', realpath($_SERVER['DOCUMENT_ROOT'])));

header("Content-type:text/html; charset=utf-8");
mb_internal_encoding('UTF-8');
date_default_timezone_set('UTC');

include PATH . 'functions.php';

$all_langs = get_langs();
$default_lang = 'en';

if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
    $user_lang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    if(in_array($user_lang, $all_langs)){
        $default_lang = $user_lang;
    }
}

if (isset($_REQUEST['lang'])){
    if(in_array($_REQUEST['lang'], $all_langs)){
        $_SESSION['install']['lang'] = $_REQUEST['lang'];
        header('Location: ' . $_SERVER['SCRIPT_NAME']);die;
    }
}

$is_lang_selected = isset($_SESSION['install']['lang']);
$lang = $is_lang_selected ? $_SESSION['install']['lang'] : $default_lang;
define('LANG', $lang);

include PATH . DS . 'languages' . DS . LANG . DS . "language.php";

$steps = array(
    array('id' => 'start', 'title' => LANG_STEP_START),
    array('id' => 'license', 'title' => LANG_STEP_LICENSE),
    array('id' => 'php', 'title' => LANG_STEP_PHP_CHECK),
    array('id' => 'paths', 'title' => LANG_STEP_PATHS),
    array('id' => 'site', 'title' => LANG_STEP_SITE),
    array('id' => 'database', 'title' => LANG_STEP_DATABASE),
    array('id' => 'admin', 'title' => LANG_STEP_ADMIN),
    array('id' => 'config', 'title' => LANG_STEP_CONFIG),
    array('id' => 'cron', 'title' => LANG_STEP_CRON),
    array('id' => 'finish', 'title' => LANG_STEP_FINISH),
);

$current_step = 0;

if (is_ajax_request()){
    $step = $steps[(int)(isset($_POST['step']) ? $_POST['step'] : 0)];
    $is_submit = isset($_POST['submit']);
    echo json_encode( run_step($step, $is_submit) );
    exit();
}

$step_result = run_step($steps[$current_step], false);

echo render('main', array(
    'steps'            => $steps,
    'is_lang_selected' => $is_lang_selected,
    'lang'             => LANG,
    'current_step'     => $current_step,
    'step_html'        => $step_result['html'],
    'langs'            => get_langs()
));
