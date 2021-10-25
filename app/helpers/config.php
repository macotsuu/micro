<?php
    use Micro\Configuration;

    if (!function_exists('config')) {
        function config(): Configuration
        {
            return Configuration::getInstance();
        }
    }