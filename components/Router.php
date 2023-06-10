<?php

namespace components;

class Router
{
    private array $routes;

    public function __construct()
    {
        $routesPath = ROOT . '/frontend/config/routes.php';
        $this->routes = require($routesPath);
    }

    private function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    public function run()
    {
        // Получить строку запроса
        $uri = $this->getURI();

        // Проверить наличие такого запроса в routes.php
        foreach ($this->routes as $uriPattern => $path) {
            /*
            Сравниваем $uriPattern и $uri
            $uri - где ищем (запрос, который набрал пользователь)
            $uriPattern - что ищем (совпадение из правила)
            $path - кто обрабатывает
            */
            if (preg_match("~$uriPattern~", $uri)) {
                // Получаем внутренний путь из внешнего согласно правилу
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);

                // Если есть совпадение, определить какой контроллер и action обрабатывают запрос
                $namespace = 'frontend\\controllers\\';
                $parameters = explode('/', $internalRoute);
                $controllerName = array_shift($parameters) . 'Controller';
                $controllerName = $namespace . ucfirst($controllerName);
                $actionName = 'action' . ucfirst((array_shift($parameters)));

                // Создать объект, вызвать action
                $controllerObject = new $controllerName();
                $result = call_user_func_array(array($controllerObject, $actionName), array($parameters));

                if ($result != null) {
                    break;
                }
            }
        }
    }

}