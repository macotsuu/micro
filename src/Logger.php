<?php
    namespace Micro;

    use Exception;

    class Logger
    {
        private string $logPath;

        public function __construct() {
            $this->logPath = dirname(__DIR__) . '/logs/';
        }

        public function log(string $path, string $message) {
            try {
                $dirlog = $this->logPath . $path . '.log';

                if (!file_exists($dirlog)) {
                    $status = mkdir(dirname($dirlog), 0777, true);
                    if ($status !== true) {
                        throw new Exception('Could not find or create directory for log file.');
                    }

                }

                $output = date('Y-m-d H:i:s') . PHP_EOL . $message . PHP_EOL;
                file_put_contents($dirlog, $output, FILE_APPEND);
            } catch (Exception $exception) {
                echo '[Message] ' . $exception->getMessage() . PHP_EOL;
                echo $exception->getTraceAsString() . PHP_EOL;
            }
        }
    }