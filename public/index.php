<?php

    use Micro\Application;
    use Micro\Configuration;

    require __DIR__ . '/../vendor/autoload.php';

    $config = Configuration::getInstance();
    $config->load(dirname(__DIR__) . '/app/config/');

    $app = Application::getInstance(dirname(__DIR__));
    $app->handle();
