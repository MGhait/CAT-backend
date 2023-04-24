<?php

use core\Authenticator;
use Http\Forms\LoginForm;

// login the user if the credentials match.
$_SESSION['id']=git_id($_POST['email']);

$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password']
]);


$signin = (new Authenticator)->attempt(
    $attributes['email'], $attributes['password']
);
if (! $signin)
{
    $form->error(
        'email','No matching account found for that email address and password'
    )->throw();
}
    redirect('/');
