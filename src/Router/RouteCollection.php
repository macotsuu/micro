<?php

namespace Micro\Router;

class RouteCollection
{
    private array $routes;

    public function add(Route $route)
    {
        $this->routes[$route->method][] = $route;
    }

    public function match(string $method, string $path): Route|bool
    {
        foreach ($this->routes[$method] as $route) {
            $regex = $route->pattern;
            foreach ($this->params($regex) as $param) {
                $regex = str_replace($param,
                    '(?P<' . trim($param, '{\}') . '>[^/]++)',
                    $regex
                );
            }

            $pattern = '#^' . $regex . '$#sD';
            $path = '/' . rtrim(ltrim(trim($path), '/'), '/');
            if ($route->method === $method && preg_match($pattern, $path, $matches)) {
                $values = array_filter($matches, static function ($key) {
                    return is_string($key);
                }, ARRAY_FILTER_USE_KEY);

                foreach ($values as $key => $value) {
                    $route->params[$key] = $value;
                }

                return $route;
            }
        }

        return false;
    }

    public function params(string $pattern): array
    {
        preg_match_all('/{[^}]*}/', $pattern, $matches);
        return reset($matches) ?? [];
    }
}