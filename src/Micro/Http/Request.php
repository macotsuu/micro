<?php
    namespace Micro\Http;

    class Request
    {
        public string $method;
        public string $path;

        public array $headers;
        public array $query;
        public array $body;
        public array $files;
        public array $cookies;
        public array $params;
        public array $session;

        public function __construct()
        {
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];

            $this->body = $_POST ?? [];
            $this->query = $_GET ?? [];
            $this->files = $_FILES ?? [];
            $this->cookies = $_COOKIE ?? [];
            $this->session = $_SESSION ?? [];

            $this->withHeaders();
        }

        public function withHeaders(): Request
        {
            foreach ($_SERVER as $header => $value) {
                if (
                    preg_match('/^HTTP_/', $header) ||
                    preg_match('/^PHP_AUTH_/', $header) ||
                    preg_match('/^REQUEST_/', $header)
                ) {
                    if (str_starts_with('HTTP_', $header)) {
                        if (str_starts_with($header, 'HTTP_')) {
                            $this->headers[substr($header, 5)] = $value;
                        }

                        if (in_array($header, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                            $this->headers[$header] = $value;
                        }
                    }
                    $this->headers[$header] = $value;
                }
            }

            return $this;
        }
    }