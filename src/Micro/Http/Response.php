<?php
    namespace Micro\Http;

    use Micro\Enums\Dirs;
    use Micro\Enums\HTTPStatus;
    use Micro\Template\Template;

    use function count;
    use const PHP_OUTPUT_HANDLER_FLUSHABLE;
    use const PHP_OUTPUT_HANDLER_REMOVABLE;

    class Response
    {
        private array $headers;
        private string $body;
        private string $statusText;
        private string $protocolVersion = '2.0';
        private HTTPStatus $statusCode;

        private Template $template;

        public function __construct()
        {
            $this->withBody('');
            $this->withStatus(HTTPStatus::OK);
        }

        public function render(string $tmpl, array $data = [])
        {
            $template = $this->template->render($tmpl, $data);

            $this->send($template);
        }

        public function redirect(string $location, $status = HTTPStatus::HTTP_MOVED_PERMANENTLY)
        {
            $this->headers['Location'] = $location;
            $this->withStatus($status);

            $this->sendHeaders()->sendBody()->end();
        }

        public function send(string $data)
        {
            $this->withBody($data)->sendHeaders()->sendBody()->end();
        }

        public function setTemplateEngine(Template $template)
        {
            $this->template = $template;
        }

        private function withBody($body): Response
        {
            $this->body = $body;

            return $this;
        }

        public function withStatus(HTTPStatus $statusCode, string $text = null): Response
        {
            $this->statusCode = $statusCode;

            if (null === $text) {
                $this->statusText = HTTPStatus::getMessage($statusCode);

                return $this;
            }

            return $this;
        }

        private function sendHeaders(): Response
        {
            if (headers_sent()) {
                return $this;
            }

            if ($this->statusCode->value >= 100 && $this->statusCode->value < 200 || in_array($this->statusCode->value, [204, 304])) {
                $this->withBody(null);

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
                    $this->body = null;

                    if ($length) {
                        $this->headers['Content-Length'] = $length;
                    }
                }
            }

            foreach ($this->headers as $header => $values) {
                $replace = 0 === strcasecmp($header, 'Content-Type');
                header($header.': '.$values, $replace, $this->statusCode->value);
            }

            header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode->value, $this->statusText), true, $this->statusCode->value);

            return $this;
        }

        private function sendBody(): Response
        {
            echo $this->body;

            return $this;
        }


        private function end()
        {
            $status = ob_get_status(true);
            $level = count($status);
            $flags = PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE;
    
            while ($level-- > 0 && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
                ob_get_flush();
            }
        }
    }