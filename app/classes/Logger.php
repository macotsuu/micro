<?php
    class Logger
    {
        public static function log(string $file, string $message): void
        {
            $logfile = LOGS_DIR . "/" . $file . ".log";

            if (!is_dir(dirname($logfile))) {
                mkdir(dirname($logfile), 777, true);
            }

            $time = date("Y-m-d H:i:s", time());
            $log = "[$time] $message" . PHP_EOL;

            file_put_contents($logfile, $log, FILE_APPEND);
        }
    }