<?php
    use Micro\Application;
    use Micro\Http\Request;

    require __DIR__ . '/../vendor/autoload.php';

    $app = Application::getInstance(dirname(__DIR__));

    $request = new Request();
    $response = $app->handle($request);

    $response->send();
