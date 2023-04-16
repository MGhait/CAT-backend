<?php

$config = require ('config.php');
$db = new Database($config['database']);

$heading = 'Note';

$note = $db -> query("SELECT * FROM notes WHERE id = :id",[
    'id'=>$_GET["id"]
])->fetch(PDO::FETCH_ASSOC);
//dd($note['user_id']);

if (! $note) {
    abort();
}
$currentUserId = 1;
if ($note['user_id'] != $currentUserId)
{
    abort(Response::FORBIDDEN);
}
require "views/note.view.php";
