<?php

use core\Response;

function dd($value)
{
echo '<pre>';
    var_dump($value);
    echo '</pre>';
die();
}

function urlIs($value){
return $_SERVER['REQUEST_URI'] == $value;
}
function authorize($condition, $status = Response::FORBIDDEN)
{
    if (! $condition) {
        abort($status);
    }
}

function abort($code = 404)
{
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();

}

function base_bath($path)
{
    return BASE_PATH . $path;
}
function view($path, $attributes = [])
{
    extract($attributes);
    require base_bath('views/'.$path);
}

function redirect($path) {
    header("location:{$path}");
    exit();
}

function login($user) {
    $_SESSION['user'] = [
        'email' => $user['email']
    ];

    session_regenerate_id(true);
}
function logout() {
    $_SESSION = [];
    session_destroy();

    $parms = session_get_cookie_params();
    setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
}

function old($key, $default = '') {
    return core\Session::get('old')[$key] ?? $default ;
}