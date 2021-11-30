<?php
    namespace Micro\Router\Middleware;

    use Micro\Http\Request;
    use Micro\Http\Response;

    interface MiddlewareInterface
    {
        public function process(Request $request, MiddlewareDispatcher $handler): Response;
    }