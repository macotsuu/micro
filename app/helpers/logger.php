<?php
    use Micro\Logger;

    if (!function_exists('logger')) {
        function logger(): Logger
        {
            return new Logger();
        }
    }
