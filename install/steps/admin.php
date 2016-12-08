<?php

function step($is_submit){

    $is_external_users = $_SESSION['install']['db']['users_exists'];

    if ($is_submit){

        if ($is_external_users){
            return array(
                'error' => false,
            );
        }

        return check_admin();
    }

    $result = array(
        'html' => render('step_admin', array(
            'is_external_users' => $is_external_users,
            'users_table' => $_SESSION['install']['db']['users_table']
        ))
    );

    return $result;

}

function check_admin(){

    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if (!$nickname || !$email || !$pass1 || !$pass2){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_ERROR
        );
    }

    if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $email)){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_EMAIL_ERROR
        );
    }

    if ($pass1 != $pass2){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_PASS_ERROR
        );
    }

    create_admin($nickname, $email, $pass1);

    return array(
        'error' => false,
    );

}

function create_admin($nickname, $email, $password){

    $db = $_SESSION['install']['db'];

    $mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);

    $mysqli->set_charset('utf8');

    $password_salt = md5(implode(':', array($password, session_id(), time(), rand(0, 10000))));
    $password_salt = substr($password_salt, rand(1,8), 16);
    $password_hash = md5(md5($password) . $password_salt);

    $sql = "UPDATE {$db['prefix']}users SET nickname='{$nickname}', email='{$email}', password = '{$password_hash}', password_salt = '{$password_salt}' WHERE id = 1";

    $mysqli->query($sql);

}
