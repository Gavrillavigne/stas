<?php

spl_autoload_register(function ($class) {
    if (!loadClass($class)) {
        loadVendor($class);
    }
});

/**
 * @param $class
 * @return bool
 */
function loadClass($class): bool
{
    $namespaces = [
        '' => '/',
        'frontend\controllers\\' => '/frontend/controllers/'
    ];

    foreach ($namespaces as $namespace => $path) {
        $file = ROOT . $path . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            // Подключить файл класса
            require $file;
            return true;
        }
    }

    return false;
}

/**
 * @param string $class
 * @return void
 */
function loadVendor(string $class): void
{
    $packages = [
        'vlucas/phpdotenv'
    ];

    $namespaces = [
        'Dotenv\\' => '/src/'
    ];

    foreach ($namespaces as $namespace => $path) {
        $pos = strpos($class, $namespace);
        if ($pos !== false) {
            $class = str_replace($namespace, $path, $class);
        }
    }

    foreach ($packages as $package) {
        $file = ROOT . '/vendor/' . $package . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}