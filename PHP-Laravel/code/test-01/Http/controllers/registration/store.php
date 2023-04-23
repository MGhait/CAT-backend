<?php

use core\App;
use core\Database;
use core\Validator;
//dd($_POST);

$email = $_POST['email'];
$password = $_POST['password'];

// validate the form inputs
$errors = [];
if (! Validator::email($email)) {
    $errors['email'] = 'Please provide valid email address';
}
if (! Validator::string($password , 7, 255)) {
    $errors['password'] = 'Please provide a password at least 7 characters';
}
//dd($errors);
if (! empty($errors)) {
     return view('registration/create.view.php',[
        'errors' => $errors
     ]);
//    dd('errors');
}

// check if the account already exists
$db = App::resolve(Database::class);
$user = $db->query('SELECT * FROM users WHERE email = :email',[
    'email' => $email
])->find();
//dd($user);
if ($user) {
    // if yes, redirect to a login page.
    header('location: /');
    exit();
//    dd($_SESSION);
} else {
    // if not, save one to the database
    $db->query('INSERT INTO users(email,password) VALUE (:email, :password)',[
        'email' => $email,
        'password' => password_hash($password,PASSWORD_BCRYPT)
    ]);
    //mark that the user has logged in
   login([
       'email' => $email
   ]);
//    dd($_SESSION);
    header('location: /');
    exit();
}

