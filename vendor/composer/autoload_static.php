<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteda84b21ec0aaa48a3faa4096b113f2d
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Symfony\\Component\\Process\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/process',
            ),
            'Symfony\\Component\\OptionsResolver\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/options-resolver',
            ),
        ),
    );

    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/..' . '/kzykhys/git/src',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticIniteda84b21ec0aaa48a3faa4096b113f2d::$prefixesPsr0;
            $loader->fallbackDirsPsr0 = ComposerStaticIniteda84b21ec0aaa48a3faa4096b113f2d::$fallbackDirsPsr0;

        }, null, ClassLoader::class);
    }
}
