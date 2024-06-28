<?php

namespace App;

use Exception;

class Router
{
    /**
     * @var array $routes
     */
    protected array $routes = [];


    /**
     * Add a route to the router
     */
    public function addRoute($route, $controller, $action): void
    {
        $this->routes[$route] = [
            'Controllers' => $controller,
            'action' => $action
        ];
    }


    /**
     * Route dispatcher
     * @param string $uri
     * @return void
     * @throws Exception
     */
    public function dispatch(string $uri): void
    {
        $found = false;
        foreach ($this->routes as $route => $config) {
            $pattern = str_replace('/', '\/', $route);
            $pattern = preg_replace('/{[^\/]+}/', '([^\/]+)', $pattern);
            $pattern = "/^$pattern$/";
            if (preg_match($pattern, $uri, $matches)) {
                $controller = $config['Controllers'];
                $action = $config['action'];
                if (count($matches) > 1) {
                    $params = array_slice($matches, 1);
                    $controller = new $controller;
                    call_user_func_array([$controller, $action], $params);
                } else {
                    $controller = new $controller;
                    $controller->$action();
                }
                $found = true;
                break;
            }
        }
        if ($found === false) {
            throw new Exception('No route defined for this URI: ' . $uri);
        }
    }
}
	