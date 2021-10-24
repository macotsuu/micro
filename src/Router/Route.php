<?php

namespace Micro\Router;

class Route
{
    public string $pattern;
    public string $method;
    public string $module;
    public array $params = [];
}