<?php
function admin_credentials_path(){
    return __DIR__ . '/admin.txt';
}

function admin_credentials_load(){
    $path = admin_credentials_path();
    if(!file_exists($path)){
        return null;
    }

    $raw = trim((string) file_get_contents($path));
    if($raw === '' || strpos($raw, ':') === false){
        return null;
    }

    list($username, $password) = explode(':', $raw, 2);
    $username = trim($username);
    $password = trim($password);

    if($username === '' || $password === ''){
        return null;
    }

    return [
        'username' => $username,
        'password' => $password
    ];
}

function admin_credentials_exist(){
    return admin_credentials_load() !== null;
}

function admin_password_matches($input_password, $stored_password){
    if(strpos($stored_password, '$2y$') === 0 || strpos($stored_password, '$argon') === 0){
        return password_verify($input_password, $stored_password);
    }

    return hash_equals($stored_password, $input_password);
}

function admin_credentials_save($username, $password){
    $path = admin_credentials_path();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    if(!is_dir(dirname($path))){
        mkdir(dirname($path), 0755, true);
    }

    return file_put_contents($path, trim($username) . ':' . $hash, LOCK_EX) !== false;
}

function admin_credentials_upgrade_if_needed($username, $password, $stored_password){
    if(strpos($stored_password, '$2y$') === 0 || strpos($stored_password, '$argon') === 0){
        return;
    }

    admin_credentials_save($username, $password);
}
?>
