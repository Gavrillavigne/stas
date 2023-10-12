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
        'app\controllers\\' => '/app/controllers/',
        'app\services\oauth\\' => '/app/services/oauth/',
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
    $namespaces = [
        'Dotenv\\' => [
            'path' => '/src/',
            'package' => 'vlucas/phpdotenv'
        ]
    ];

    foreach ($namespaces as $namespace => $params) {
        $pos = strpos($class, $namespace);
        if ($pos !== false) {
            $class = str_replace($namespace, $params['path'], $class);
            $file = ROOT . '/vendor/' . $params['package'] . str_replace('\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require $file;
                break;
            }
        }
    }
}