<?php
    namespace Micro\Router\Middleware;

    use Micro\Http\Request;
    use Micro\Http\Response;

    class MiddlewareDispatcher
    {
        private MiddlewareCollection $middlewares;

        public function __construct(MiddlewareCollection $middlewares)
        {
            $this->middlewares = $middlewares;
        }

        public function handle(Request $request): Response
        {
            $this->middlewares->next();

            if ($this->middlewares->valid()) {
                $response = $this->middlewares->current()->process($request, $this);
            }

            $this->middlewares->set(-1);

            return $response ?? new Response();
        }

    }