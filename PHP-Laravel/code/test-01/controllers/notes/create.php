<?php
require 'Validator.php';

$config = require ('config.php');
$db = new Database($config['database']);
$heading = 'Create Note';

if($_SERVER['REQUEST_METHOD']== 'POST')
{
    $errors =[];
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
require 'views/notes/create.view.php';