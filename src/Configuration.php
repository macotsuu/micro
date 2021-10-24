<?php

    namespace Micro;

    class Configuration
    {
        private static null|Configuration $instance = null;
        private array $items = [];

        public static function getInstance(): Configuration
        {
            if (is_null(self::$instance)) {
                self::$instance = new Configuration();
            }

            return self::$instance;
        }

        public function load($dir) {
            foreach (glob($dir . '*.php') as $file) {
                $explode = explode('/', $file);
                $name = substr($explode[count($explode) - 1], 0, -4);

                $this->items[$name] = (require $file)[$name];
            }
        }

        public function get(string $key, string $default = null): string|array {
            $items = $this->items;

            foreach (explode('.', $key) as $segment) {
                if (!is_array($items) || !array_key_exists($segment, $items)) {
                    return $default;
                }

                $items = &$items[$segment];
            }

            return $items;
        }

        public function set(string $keys, mixed $value) {
            if (is_array($keys)) {
                foreach ($keys as $key => $value) {
                    $this->set($key, $value);
                }

                return;
            }

            $items = &$this->items;
            foreach (explode('.', $keys) as $key) {
                if (!isset($items[$key]) || !is_array($items[$key])) {
                    $items[$key] = [];
                }

                $items = &$items[$key];
            }

            $items = $value;
        }
    }