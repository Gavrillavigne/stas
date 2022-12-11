<?php

spl_autoload_register(function ($class) {

    $classFound = false;
    $file = ROOT . '/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        $classFound = true;
        require $file;
    }

    if (!$classFound) {
        loadVendor($class);
    }
});

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