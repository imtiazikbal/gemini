<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit219684a207321305a46b55814690a375
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Imtiaz\\LaravelGemini\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Imtiaz\\LaravelGemini\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit219684a207321305a46b55814690a375::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit219684a207321305a46b55814690a375::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit219684a207321305a46b55814690a375::$classMap;

        }, null, ClassLoader::class);
    }
}