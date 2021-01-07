<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit814e92839cba84b2199a7d36ab0a6e06
{
    public static $files = array (
        '7509c762dfa99713be391815441d8091' => __DIR__ . '/..' . '/aaronlee/swoPolaris/src/Supper/Helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SwoPolaris\\' => 11,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SwoPolaris\\' => 
        array (
            0 => __DIR__ . '/..' . '/aaronlee/swoPolaris/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit814e92839cba84b2199a7d36ab0a6e06::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit814e92839cba84b2199a7d36ab0a6e06::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}