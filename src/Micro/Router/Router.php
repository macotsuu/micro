<?php
    namespace Micro\Router;

    use ArrayObject;
    use Closure;

    class Router
    {
        private RouteCollection $routes;

        public function __construct()
        {
            $this->routes = new RouteCollection();
        }

        /**
         * @param string $path
         * @param string|Closure $callback
         *
         * @return $this
         */
        public function get(string $path, string|Closure $callback): Router
        {
           $this->addRoute('GET', $path, $callback);

           return $this;
        }

        /**
         * @param string $path
         * @param string|Closure $callback
         *
         * @return $this
         */
        public function post(string $path, string|Closure $callback): Router
        {
            $this->addRoute('POST', $path, $callback);

            return $this;
        }

        /** Return Routes Collection
         *
         * @return RouteCollection
         */
        public function getRoutes(): RouteCollection
        {
            return $this->routes;
        }

        /**
         *
         * @param string $method
         * @param string $path
         * @param string|Closure $callback
         *
         * @retrun void
         */
        private function addRoute(string $method, string $path, string|Closure $callback): void
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