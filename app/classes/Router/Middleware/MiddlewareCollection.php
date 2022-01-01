<?php

    namespace Router\Middleware;

    class MiddlewareCollection
    {
        private int $position;
        private array $middlewares = [];

        public function __construct(int $position = 0)
        {
            $this->position = $position;
        }

        public function push(MiddlewareInterface $middleware): void
        {
            $this->middlewares[] = $middleware;
        }

        public function current(): MiddlewareInterface
        {
            return $this->middlewares[$this->position];
        }

        public function next(): void
        {
            ++$this->position;
        }

        public function get(): array
        {
            return $this->middlewares;
        }

        public function set(int $position)
        {
            $this->position = $position;
        }

        public function valid(): bool
        {
            return isset($this->middlewares[$this->position]);
        }



    }