<?php

    namespace Micro;

    use Memcached;

    class Cache
    {
        private static null|Memcached $instance = null;

        public static function getInstance(): Memcached
        {
            if (is_null(self::$instance)) {
                self::$instance = new Memcached();
                self::$instance->addServer('localhost', '11211');
            }

            return self::$instance;
        }

    }