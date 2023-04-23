<?php

// find the corresponding note

use core\Database;
use core\App;
use core\Validator;

$db = App::resolve(Database::class);

$currentUserId = 1 ;
$note = $db->query("SELECT * FROM notes WHERE id = :id", [
    'id' => $_POST["id"]
])->findOrFail();

// authorize that the current user can dit the note
authorize($note['user_id'] == $currentUserId);

// validate the form
$errors= [];
$invalidNum =250;
if(! Validator::string($_POST['body'],1,$invalidNum)){
    $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
}

// if no validation errors, update the record in the notes database table.
if (count($errors)) {
    return view('notes/edit.view.php',[
       'heading'=> 'Edit Note',
       'errors' => $errors,
       'note' => $note
    ]);
}

$db->query('update notes set body = :body where  id = :id',[
    'id' => $_POST['id'],
    'body' => $_POST['body']
]);

// redirect the user

header('location: /notes');
die();