<?php
    namespace Micro\Enums;

    enum HTTPStatus: int {
        case OK = 200;
        case HTTP_MOVED_PERMANENTLY = 301;
        case HTTP_FOUND = 302;
        case NOT_FOUND = 404;
        case HTTP_INTERNAL_SERVER_ERROR = 500;

        public static function getMessage(self $value): string {
            return match ($value) {
                self::OK => 'OK',
                self::HTTP_FOUND => 'Found',
                self::NOT_FOUND => 'NOT FOUND',
                self::HTTP_MOVED_PERMANENTLY => 'Moved Permanently',
                self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Error',
                default => 'unknown status'
            };
        }

        public function message(): string {
            return HTTPStatus::getMessage($this);
        }
    }