<?php

namespace Micro\Router;

class Router
{
    private RouteCollection $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function run(string $path, string $method)
    {
        return $this->routes->match($method, $path);
    }

    public function get(string $path, string $module)
    {
        $this->addRoute('GET', $path, $module);
    }

    public function post(string $path, string $module)
    {
        $this->addRoute('POST', $path, $module);
    }

    private function addRoute(string $method, string $path, string $module)
    {
        $route = new Route();
        $route->method = $method;
        $route->pattern = $path;
        $route->module = $module;

        $this->routes->add($route);
    }
}