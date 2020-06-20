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

    $nickname = (string) @$_POST['nickname'];
    $email    = (string) @$_POST['email'];
    $pass1    = (string) @$_POST['pass1'];
    $pass2    = (string) @$_POST['pass2'];

    if (!$nickname || !$email || !$pass1 || !$pass2){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_ERROR
        );
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== $email){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_EMAIL_ERROR
        );
    }

    if ($pass1 !== $pass2){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_PASS_ERROR
        );
    }

    if (mb_strlen($pass1) < 6){
        return array(
            'error' => true,
            'message' => sprintf(LANG_VALIDATE_MIN_LENGTH, 'password', 6)
        );
    }

    if (mb_strlen($pass1) > 72){
        return array(
            'error' => true,
            'message' => sprintf(LANG_VALIDATE_MAX_LENGTH, 'password', 72)
        );
    }

    $pass_hash = password_hash($pass1, PASSWORD_BCRYPT);

    if($pass_hash === false){
        return array(
            'error' => true,
            'message' => LANG_ADMIN_PASS_HASH_ERROR
        );
    }

    create_admin($nickname, $email, $pass_hash);

    return array(
        'error' => false,
    );

}

function create_admin($nickname, $email, $pass_hash){

    $db = $_SESSION['install']['db'];

    $mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);

    $mysqli->set_charset('utf8');

    $sql = "UPDATE {$db['prefix']}users SET nickname='{$nickname}', email='{$email}', password_hash = '{$pass_hash}' WHERE id = 1";

    $mysqli->query($sql);

    $auth_data = array(
        'ip'          => sprintf('%u', ip2long('127.0.0.1')),
        'access_type' => '---\ntype: desktop\nsubj: null\n',
        'auth_token'  => hash('sha512', md5(md5($nickname.$pass_hash).md5($email))),
        'user_id'     => 1
    );

    $sql = "INSERT INTO {$db['prefix']}users_auth_tokens (`ip`, `access_type`, `auth_token`, `user_id`) VALUES ('{$auth_data['ip']}', '{$auth_data['access_type']}', '{$auth_data['auth_token']}', '{$auth_data['user_id']}')";

    $mysqli->query($sql);

    setcookie('icms[auth]', $auth_data['auth_token'], time()+300, '/', null, false, true);

}
