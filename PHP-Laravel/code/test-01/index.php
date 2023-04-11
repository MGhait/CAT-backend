<?php

require 'function.php';
// require 'router.php';
require 'Database.php';

$config = require ('config.php');
$db = new Database($config['database']);

$id = $_GET['id'];

$query = "SELECT * FROM posts where id = ?";
$posts = $db->query($query,[$id])-> fetchAll();

foreach ($posts as $post){
    echo "<li>". $post['NAME'] ."</li>";
} 