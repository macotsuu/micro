<?php
    namespace Micro;

    use Exception;
    use Micro\Router\Router;

    class Application {
        private static array $instances = [];

        private string $basePath;
        private Router $router;

        protected function __clone() { }
        public function __wakeup()
        {
            throw new Exception("Cannot unserialize a singleton.");
        }

        public static function getInstance(string $basePath = null): Application
        {
            $cls = static::class;
            if (!isset(self::$instances[$cls])) {
                self::$instances[$cls] = new static($basePath);
            }

            return self::$instances[$cls];
        }

        private function __construct(string $basePath = null) {
            if (!is_null($basePath)) {
                $this->basePath = $basePath;
            }

            $this->loadRoutes();
        }

        public function getBasePath(): string {
            return $this->basePath;
        }

        public function handle(): void {
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
            $method = $_SERVER['REQUEST_METHOD'];

            $route = $this->router->run($uri, $method);
            if ($route !== false) {
                $path = dirname(__DIR__) . "/app/modules/$route->module.php";

                if (file_exists($path)) {
                    include $path;
                }
            } else {
                echo "$uri - not found";
            }
        }

        private function loadRoutes() {
            $router = new Router();
            $dir = ($this->getBasePath() . '/routes/');

            foreach (glob($dir . '*.php') as $file) {
                (require($file))($router);
            }

            $this->router = $router;
        }
    }