<?php
use core\Database;
use core\App;
$db = App::resolve(Database::class);

$notes = $db -> query("SELECT * FROM notes WHERE user_id = :id",[
    'id' => $_SESSION['id']
])->get();

view("notes/index.view.php" ,[
    'heading'=> 'My Notes',
    'notes'=>$notes
]);

