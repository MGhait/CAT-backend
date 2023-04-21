<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);
view("notes/edit.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=> [],
    'note'=>$note
]);