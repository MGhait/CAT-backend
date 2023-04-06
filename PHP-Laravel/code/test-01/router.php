<?php
//we use pars_url() to get url separated form any possible queries
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];  // this give as path 'url only'

/*
if ($uri == '/'){
require 'controllers/index.php';
} else if ($uri == '/about'){
require 'controllers/about.php';
} else if ($uri == '/contact'){
require 'controllers/contact.php';
}
this is a easy way to do our chicks

*/


$routes = [
'/' => 'controllers/index.php',
'/about' => 'controllers/about.php',
'/contact' => 'controllers/contact.php'
];

function abort($code=404){
http_response_code($code);
require "views/{$code}.php";
die();
}

function routeToController($uri,$routes){
if (array_key_exists($uri,$routes)){
require $routes[$uri];
} else {
abort();
//    abort(404);
}
}

routeToController($uri,$routes);