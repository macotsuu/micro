<?php
    namespace Router;

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
            if (!is_null($method)) {
                if (!empty($this->routes[$method])) {
                    return $this->routes[$method];
                }

                return [];
            }
            
            return $this->routes;
        }
    }
