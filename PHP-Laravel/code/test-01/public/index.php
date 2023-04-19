<?php

const BASE_PATH = __DIR__ . '/../';
require BASE_PATH . 'core/function.php';



spl_autoload_register(function ($class) {
    $class= str_replace('\\',DIRECTORY_SEPARATOR,$class);
   require base_bath("{$class}.php");
});
require base_bath('core/router.php');
