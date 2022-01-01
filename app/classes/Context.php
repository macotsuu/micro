<?php
    use Database\Connection;
    use Http\Request;
    use Http\Response;

    class Context
    {
        public Connection $db;
        public Request $request;
        public Response $response;

        public function __construct()
        {
            $this->db = new Connection();
            $this->request = new Request();
            $this->response = new Response();
        }

        /**
         * @param int $status
         * @param string $message
         *
         * @return void
         */
        public function throw(int $status, string $message): void
        {
            $this->response->withStatus($status);

            $response = [
                "status" => $status,
                "message" => $message
            ];

            $this->json($response);
        }

        /** send response as json
         * @param array $body
         * @return void
         */

        public function json(array $body): void
        {
            $this->response->withHeader('Content-Type', 'application/json');
            $this->response->withContent(json_encode($body));

            $this->response->send();
        }

        public function redirect(string $url, int $status = 301): void
        {
            $this->set('Location', $url);
            $this->status($status);
            $this->response->withContent("");

            $this->response->send();
        }

        /** add header to response
         *
         * @param string $header
         * @param string $value
         *
         * @return $this
         */
        public function set(string $header, string $value)
        {
            $this->response->withHeader($header, $value);

            return $this;
        }

        /** set status code for response
         *
         * @param int $status
         * @param string|null $message
         *
         * @return $this
         */

        public function status(int $status, string $message = null): self
        {
            $this->response->withStatus($status, $message);

            return $this;
        }
    }