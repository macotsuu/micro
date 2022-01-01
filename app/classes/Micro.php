<?php

    use Http\Request;
    use Router\Router;
    use Router\Middleware\MiddlewareCollection;
    use Router\Middleware\MiddlewareDispatcher;
    use Router\Middleware\MiddlewareInterface;

    class Micro
    {
        /** @var Router $router */
        private Router $router;

        /** @var MiddlewareCollection $middlewares */
        private MiddlewareCollection $middlewares;

        public function __construct()
        {
            $this->router = new Router();
            $this->middlewares = new MiddlewareCollection(-1);
        }

        /** add new middleware to stack
         * @param MiddlewareInterface $middleware
         * @return $this
         */
        public function use(MiddlewareInterface $middleware): Micro
        {
            $this->middlewares->push($middleware);

            return $this;
        }

        /** add new GET route
         * @param string $path
         * @param string $callback
         *
         * @return $this
         */
        public function get(string $path, string|Closure $callback): self
        {
            $this->router->addRoute('GET', $path, $callback);

            return $this;
        }

        /** add new POST route
         * @param string $path
         * @param string|Closure $callback
         *
         * @return $this
         */
        public function post(string $path, string|Closure $callback): self
        {
            $this->router->addRoute('POST', $path, $callback);

            return $this;
        }

        /** Handle current request
         *
         * @return void
         */
        public function handle()
        {
            $ctx = new Context();
            $dispatcher = new MiddlewareDispatcher($this->middlewares);

            $ctx->response = $dispatcher->handle($ctx->request);

            foreach ($this->router->getRoutes($ctx->request->method) as $route) {
                $path = '/' . rtrim(ltrim(trim(strtok($ctx->request->path, '?')), '/'), '/');

                if (preg_match($route->route, $path, $matches)) {
                    $values = array_filter($matches, static function ($key) {
                        return is_string($key);
                    }, ARRAY_FILTER_USE_KEY);

                    foreach ($values as $key => $value) {
                        $ctx->request->params[$key] = $value;
                    }

                    if ($route->callback instanceof Closure) {
                        call_user_func_array($route->callback, [$ctx]);

                        break;
                    }

                    $instance = new $route->callback;
                    $instance->handle($ctx);

                    break;
                }
            }
        }
    }