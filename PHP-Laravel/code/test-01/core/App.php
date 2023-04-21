<?php

namespace core;

class App
{

    protected static $container;
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    public static function container()
    {
        return  static::$container;
    }

    //to make direct access from app class to bind function in Container class
    public static function bind($key, $resolver)
    {
        static::container()->bind($key, $resolver);
    }

    //to make direct access from app class to resolve function in Container class
    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}