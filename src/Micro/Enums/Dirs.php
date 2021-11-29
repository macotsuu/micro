<?php
    namespace Micro\Enums;

    enum Dirs: string
    {
        case ROUTES = 'routes';
        case CONFIG = 'config';
        case CACHE = 'cache';
        case LOG = 'logs';
        case VIEWS = 'views';

        public function path(): string {
            return match($this) {
                self::ROUTES => 'routes/',
                self::CONFIG => 'config/',
                self::CACHE => 'var/cache/',
                self::LOG => 'var/logs/',
                self::VIEWS => 'views/'
            };
        }
    }