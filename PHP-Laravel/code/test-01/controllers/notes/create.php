<?php
use core\Database;
use core\Validator;

require base_bath('core/Validator.php');

$config = require base_bath('config.php');
$db = new Database($config['database']);
$errors =[];
if($_SERVER['REQUEST_METHOD']== 'POST')
{

    $invalidNum =150;

    if(! Validator::string($_POST['body'],1,300)){
        $errors['body']="A Note Can NOT Be Empty Or More Than {$invalidNum} Characters. ";
    }

    if(empty($errors)){
        $db->query('INSERT INTO notes(body, user_id) VALUE(:body, :user_id)',[
            'body'=> $_POST['body'],
            'user_id'=> 1
        ]);
    }
}
view("notes/create.view.php" ,[
    'heading'=> 'Create Note',
    'errors'=>$errors
]);