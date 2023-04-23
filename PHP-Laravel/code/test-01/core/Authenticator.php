<?php

namespace core;

class Authenticator
{
    public function attempt($email, $password)
    {

        $user = App::resolve(Database::class)
            ->query('select * from users where email = :email',[
            'email' => $email
        ])->find();

        if ($user) {
            if (password_verify($password,$user['password'])) {
                $this->login([
                    'email' => $email
                ]);

                return true;
            }
        }
        return false;


    }

    public function login($user) {
        $_SESSION['user'] = [
            'email' => $user['email']
        ];

        session_regenerate_id(true);
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();

        $parms = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
    }

}