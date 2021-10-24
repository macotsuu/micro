<?php
    use Micro\Router\Router;

    return function (Router $router) {
        $router->get('/', 'main');
    };