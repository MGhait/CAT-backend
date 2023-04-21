<?php
use core\Database;
use core\Validator;

$config = require base_bath('config.php');
$db = new Database($config['database']);

$errors= [];

if($_SERVER['REQUEST_METHOD']== 'POST')
{
    $invalidNum =250;
    if(! Validator::string($_POST['body'],1,$invalidNum)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }
    if (! empty($errors)) {
        // validation issues
         return view("notes/create.view.php" ,[
            'heading'=> 'Create Note',
            'errors'=>$errors
        ]);
    }
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
        header('location: /notes');
        die();
}