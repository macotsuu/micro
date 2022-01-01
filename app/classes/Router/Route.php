<?php
    namespace Router;

    use Closure;

    final class Route
    {
        public function __construct(
            public string $method,
            public string $route,
            public string|Closure $callback
        ) {}
    }