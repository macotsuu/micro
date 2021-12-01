# Micro Framework
Minimal, zero dependency framework for PHP

## Installation
Add repositories in composer.json
```
    "repositories": [
        {
            "url": "https://github.com/macotsuu/micro",
            "type": "git"
        }
    ] 
```
Add the package name in require
```
    "micro/micro": "dev-master"
```
Finally execute this command for install Micro.
```
$ composer install
```
Micro requires PHP 8.0 or newer.

## Example Hello world

Create file public/index.php

```injectablephp
<?php
    
    use Micro\Http\Request;
    use Micro\Http\Response;
    use Micro\Micro;
    use Micro\Router\Middleware\MiddlewareDispatcher;
    use Micro\Router\Middleware\MiddlewareInterface;
    use Micro\Router\Router;
    
    require __DIR__ . '/../vendor/autoload.php';
    
    class Action {
        public function handle($request, $response) {
            $response->render('test.html');
        }
    }
    
    $router = new Router();
    $router->get('/', Action::class);
    
    $app = new Micro(dirname(__DIR__));
    $app->handle($router);
```

## License

Distributed under the GNU GP3 License. See `LICENSE.md` for more information.