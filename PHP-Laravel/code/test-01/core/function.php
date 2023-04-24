<?php

use core\App;
use core\Database;
use core\Response;
use core\Session;

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

function old($key, $default = '')
{
       return core\Session::get('old')[$key] ?? $default;
}
function git_id($email)
{
    $db = App::resolve(Database::class);
    $var= $db->query('select * from users where email = :email',[
        'email' => $email
    ])->find();
//dd($var);
    return $var['id'];
}