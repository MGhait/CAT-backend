<?php
//dump and die function -to check any point of our code-
function dd($value)
{
echo '<pre>';
    var_dump($value);
    echo '</pre>';
die();
}
// to get the boolean value of the current page
// we use it to make short-hand-if more readable
function urlIs($value){
return $_SERVER['REQUEST_URI'] == $value;
}

//short hand if
//=$_SERVER['REQUEST_URI'] =='/'?"bg-gray-900 text-white":"text-gray-300"