<?php
    namespace Micro;

    use Micro\Router\Router;
    use Micro\Http\Request;
    use Micro\Http\Response;

    class Micro
    {
        public function handle(Router $router)
        {
            $req = new Request();
            $res = new Response();

            foreach ($router->getRoutes()->all($_SERVER['REQUEST_METHOD']) as $route) {
                $path = '/' . rtrim(ltrim(trim(strtok($_SERVER["REQUEST_URI"], '?')), '/'), '/');

                if (preg_match($route->route, $path, $matches)) {
                    $values = array_filter($matches, static function ($key) {
                        return is_string($key);
                    }, ARRAY_FILTER_USE_KEY);

                    foreach ($values as $key => $value) {
                        $req->params[$key] = $value;
                    }

                    if (is_string($route->callback)) {
                        $instance = new $route->callback();
                        $instance->handle($req, $res);

                    } else {
                        $callback = $route->callback;
                        $callback($req, $res);
                    }

                    break;
                }
            }
        }

    }