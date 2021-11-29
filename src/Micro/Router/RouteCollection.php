<?php
    namespace Micro\Router;

    class RouteCollection
    {
        private array $routes = [];
        
        public function __construct(array $routes = [])
        {
            foreach ($routes as $route) {
                $this->routes[] = $route;
            }
        }

        public function add(Route $route): void
        {
            $this->routes[$route->method][] = $route;
        }

        public function all(string $method = null): array
        {
            return is_null($method) ? $this->routes : $this->routes[$method];
        }
    }
