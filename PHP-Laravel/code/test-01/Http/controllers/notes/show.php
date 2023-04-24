<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$currentUserId = $_SESSION['id'] ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_GET["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

view("notes/show.view.php", [
    'heading' => 'Note',
    'note' => $note
]);
