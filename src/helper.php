<?php

    use Micro\Cache;
    use Micro\Configuration;
    use Micro\Database;
    use Micro\Logger;
    use Micro\Template;
    use Micro\Application;

    if (!function_exists('app')) {
        function app(): Application {
            return Application::getInstance(dirname(__DIR__, 2 ));
        }
    }

    if (!function_exists('config')) {
        function config(): Configuration {
            return Configuration::getInstance();
        }
    }

    if (!function_exists('logger')) {
        function logger(): Logger {
            return new Logger();
        }
    }

    if (!function_exists('template')) {
        function template(): Template {
            return new Template();
        }
    }

    if (!function_exists('cache')) {
        function cache(): Memcached {
            return Cache::getInstance();
        }
    }

    if (!function_exists('sql')) {
        function sql(): Database {
            return Database::getInstance();
        }
    }