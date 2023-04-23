<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$currentUserId = 1 ;

$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

authorize($note['user_id'] == $currentUserId);

$db->query('DELETE FROM notes WHERE id = :id',[
'id'=>$_GET['id'],
]);

header('location: /notes');
exit();

