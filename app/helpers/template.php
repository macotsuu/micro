<?php
    use Micro\Template;

    if (!function_exists('template')) {
        function template(): Template
        {
            return new Template();
        }
    }