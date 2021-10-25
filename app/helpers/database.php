<?php

    use Micro\Database;

    if (!function_exists('sql')) {
        function sql(): Database {
            return Catabase::getInstance();
        }
    }