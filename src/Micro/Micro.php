<?php
    namespace Micro;

    use Micro\Enums\Dirs;
    use Micro\Http\Request;
    use Micro\Router\Router;
    use Micro\Router\Middleware\MiddlewareCollection;
    use Micro\Router\Middleware\MiddlewareDispatcher;
    use Micro\Router\Middleware\MiddlewareInterface;
    use Micro\Template\Template;

    class Micro
    {
        private string $baseDir;
        private Template $template;
        private MiddlewareCollection $middlewares;

        public function __construct(string $baseDir)
        {
            $this->middlewares = new MiddlewareCollection(-1);
            $this->baseDir = $baseDir;

            $this->template = new Template($this->getPath(Dirs::VIEWS), $this->getPath(Dirs::CACHE));
        }

        public function use(MiddlewareInterface $middleware): Micro
        {
            $this->middlewares->push($middleware);

            return $this;
        }

        public function handle(Router $router)
        {
            $request = new Request();
            $dispatcher = new MiddlewareDispatcher($this->middlewares);

            $response = $dispatcher->handle($request);
            $response->setTemplateEngine($this->template);

            foreach ($router->getRoutes()->all($request->method) as $route) {
                $path = '/' . rtrim(ltrim(trim(strtok($request->path, '?')), '/'), '/');

                if (preg_match($route->route, $path, $matches)) {
                    $values = array_filter($matches, static function ($key) {
                        return is_string($key);
                    }, ARRAY_FILTER_USE_KEY);

                    foreach ($values as $key => $value) {
                        $request->params[$key] = $value;
                    }

                    $instance = new $route->callback;
                    $instance->handle($request, $response);

                    break;
                }
            }
        }

        private function getPath(Dirs $dir): string
        {
            return $this->baseDir . '/' . $dir->path();
        }
    }