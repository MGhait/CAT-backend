<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';

$note = $db -> query("SELECT * FROM notes WHERE user_id = :id",['id'=>$_GET["id"]])->fetch(PDO::FETCH_ASSOC);
//dd($note);
require "views/note.view.php";
