<?php

require 'function.php';
require 'router.php';

// connect to our MySQL database

$dsn ="mysql:host=localhost;port=3306;dbname=laracast;user=root;password=Password;charset=utf8mb4";

$pdo = new PDO($dsn);
$statement = $pdo->prepare("SELECT * FROM posts");

$statement->execute();

// $posts = $statement -> fetchAll();

// TO AVOID DUPLICATION 
$posts = $statement -> fetchAll(PDO::FETCH_ASSOC);
// dd($posts);

// TO PRINT IT WE CAN LOOP FOR IT 
foreach ($posts as $post){
    echo "<li>". $post['NAME'] ."</li>";
} 