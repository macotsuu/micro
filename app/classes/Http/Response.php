<?php

    namespace Http;

    use function count;
    use const PHP_OUTPUT_HANDLER_FLUSHABLE;
    use const PHP_OUTPUT_HANDLER_REMOVABLE;

    class Response
    {

        /** @var array $params */
        public array $params = [];

        /** @var array $headers */
        public array $headers = [];

        /** @var string $protocolVersion */
        private string $protocolVersion = '2.0';

        /** @var string $content * */
        private string $content;

        /** @var int $statusCode */
        private int $statusCode;

        /** @var string $statusText */
        private string $statusText;

        /** @var array $statusTexts */
        public array $statusTexts = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ];

        public function __construct(?string $content = '', int $status = 200)
        {
            $this->withContent($content);
            $this->withStatus($status);
        }

        public function send(): void
        {
            $this->headers();
            $this->body();
            $this->end();
        }

        public function withContent(string $content): self
        {
            $this->content = $content;

            return $this;
        }

        public function withStatus(int $statusCode, string $text = null): self
        {
            $this->statusCode = $statusCode;

            if (null === $text) {
                $this->statusText = $this->statusTexts[$statusCode] ?: 'unknown';

                return $this;
            }

            return $this;
        }

        public function withHeader(string $header, string $value): self
        {
            $this->headers[$header] = $value;

            return $this;
        }

        private function headers(): self
        {
            if (headers_sent()) {
                return $this;
            }

            if ($this->statusCode >= 100 && $this->statusCode < 200 || in_array($this->statusCode, [204, 304])) {
                $this->withContent(null);

                unset($this->headers['Content-Type']);
                unset($this->headers['Content-Length']);
            } else {
                $charset = 'UTF-8';

                if (!isset($this->headers['Content-Type'])) {
                    $this->headers['Content-Type'] = "text/html; charset=$charset";
                }

                if (isset($this->headers['Transfer-Encoding'])) {
                    unset($this->headers['Content-Length']);
                }

                if ('HEAD' === $_SERVER['REQUEST_METHOD']) {
                    $length = $this->headers['Content-Length'];
                    $this->content = null;

                    if ($length) {
                        $this->headers['Content-Length'] = $length;
                    }
                }
            }

            foreach ($this->headers as $header => $values) {
                $replace = 0 === strcasecmp($header, 'Content-Type');
                header($header . ': ' . $values, $replace, $this->statusCode);
            }

            header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode, $this->statusText), true, $this->statusCode);

            return $this;
        }

        private function body(): self
        {
            echo $this->content;

            return $this;
        }

        private function end(): self
        {
            $status = ob_get_status(true);
            $level = count($status);
            $flags = PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE;

            while ($level-- > 0 && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
                ob_get_flush();
            }

            return $this;
        }
    }
