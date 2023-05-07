<?php

use core\Authenticator;
//use core\Session;
//use core\ValidationException;
use Http\Forms\LoginForm;

// login the user if the credentials match.
//$email = $_POST['email'];
//$password = $_POST['password'];


$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password']
]);


$signedIn = (new Authenticator)->attempt(
    $attributes['email'], $attributes['password']
);

if ($signedIn) {
    $form->error(
        'email','No matching account found for that email address and password'
    )->throw();
}

    redirect('/');


//$_SESSION['errors'] = $form->errors(); we need it to be flashed
//$_SESSION['_flash']['errors'] = $form->errors(); //  we did session class to help it
//Session::flash('errors',$form->errors());
//Session::flash('old', [
//    'email' => $_POST['email']
//]);

//return redirect('/login');
//return view('session/create.view.php',[
//    'errors' => $form->errors()
//]);
