<?php
    namespace Router\Middleware;

    use Http\Request;
    use Http\Response;

    interface MiddlewareInterface
    {
        public function process(Request $request, MiddlewareDispatcher $handler): Response;
    }