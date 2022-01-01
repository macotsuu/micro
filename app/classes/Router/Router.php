<?php
    namespace Router;

    use Closure;

    class Router
    {
        /** @var RouteCollection $routes */
        private RouteCollection $routes;

        public function __construct()
        {
            $this->routes = new RouteCollection();
        }

        /** Return all routes
         *
         * @param string $method
         * @return array
         */
        public function getRoutes(string $method = 'GET'): array
        {
            return $this->routes->all($method);
        }

        /**
         *
         * @param string $method
         * @param string $path
         * @param string|Closure $callback
         *
         * @retrun void
         */
        public function addRoute(string $method, string $path, string|Closure $callback): void
        {
            $route = new Route($method, $this->pattern($path), $callback);

            $this->routes->add($route);
        }

        /**
         * @param string $pattern
         * @return string
         */
        private function pattern(string $pattern): string
        {
            preg_match_all('/{[^}]*}/', $pattern, $matches);

            $regex = $pattern;
            $params = reset($matches) ?? [];

            foreach ($params as $param) {
                $regex = str_replace($param,
                    '(?P<' . trim($param, '{\}') . '>[^/]++)',
                    $regex
                );
            }

            return "#^$regex$#sD";
        }
    }