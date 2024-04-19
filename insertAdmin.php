<?php
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    include_once('domain/User.php');
    include_once('database/dbUsers.php');

    $user = Array();
    $user['first_name'] = 'vmsroot';
    $user['last_name'] = '';
    $user['email'] = 'vmsroot';
    $user['password'] = password_hash('vmsroot', PASSWORD_BCRYPT);
    $user['account_type'] = 'super admin';
    $user['role'] = 'Root User';
    $USER = make_a_user($user);
    $result = add_user($USER);
    if ($result) {
        echo 'ROOT USER CREATION SUCCESS';
    } else {
        echo 'USER ALREADY EXISTS';
    }
?>