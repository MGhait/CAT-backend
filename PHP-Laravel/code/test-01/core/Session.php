<?php

namespace core;

class Session
{
    public static function has($key) {
        return (bool) static::get($key);
    }

    public static function put($key, $value){
        $_SESSION['key'] = $value;
    }

    public  static function get($key, $default = null) {
//        if (isset($_SESSION['_flash'][$key])) {
//            return $_SESSION['_flash'][$key];
//        }
//        return $_SESSION['key'] ?? $default; we can make it with shorthand
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    public static function flash($key, $value) {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function unflash() {
        unset($_SESSION['_flash']);

    }

    public static function flush() {
        $_SESSION = [];
    }

    public static function destroy() {
        //        $_SESSION = []; we can use here flush method form session
        static::flush();
        session_destroy();

        $parms = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $parms['path'], $parms['domain'], $parms['secure'], $parms['httponly']);
    }

}