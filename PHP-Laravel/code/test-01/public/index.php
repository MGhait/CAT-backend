<?php

use core\Session;
use core\ValidationException;

const BASE_PATH = __DIR__ . '/../';
require BASE_PATH .'vendor/autoload.php';
session_start();

require BASE_PATH . 'core/function.php';



//spl_autoload_register(function ($class) {
//    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
//   require base_bath("{$class}.php");
//}); we will use composer autoload instead

//require BASE_PATH .'vendor/autoload.php';

require base_bath('bootstrap.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

try {
    $router->route($uri,$method);
} catch (ValidationException $exception) {
    Session::flash('errors',$exception->errors);
    Session::flash('old',$exception->old);

    return redirect($router->previousUrl());
}

//unset($_SESSION['flash']); we use class Session on it
Session::unflash();
