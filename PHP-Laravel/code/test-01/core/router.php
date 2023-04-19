<?php
$routes = require base_bath('routes.php');

function abort($code=404){
    http_response_code($code);
    require base_bath("views/{$code}.php");
    die();
}
function routeToController($uri,$routes){
    if (array_key_exists($uri,$routes)){
        require base_bath($routes[$uri]);
    }
    else {
        abort();
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
routeToController($uri,$routes);
