<?php
    use Micro\Cache;

    if (!function_exists('cache')) {
        function cache(): Memcached
        {
            return Cache::getInstance();
        }
    }