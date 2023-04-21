<?php

const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});

require base_bath('bootstrap.php');

$router=new core\Router();

$routes = require base_bath('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];

$router->route($uri,$method);
