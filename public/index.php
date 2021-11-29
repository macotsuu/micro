<?php

    use Micro\Micro;
    use Micro\Router\Router;

    require __DIR__ . '/../vendor/autoload.php';

    $router = new Router();
    $router->get('/', function ($req, $res) {
        print_r("<pre>");
        print_r($req);
        print_r("</pre>");
    });

    $app = new Micro();
    $app->handle($router);