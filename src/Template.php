<?php

    namespace Micro;

    class Template
    {
        public function render(string $tmpl, string $data): void {
            $path = dirname(__DIR__) . '/app/views/';
            $template = $path . $tmpl . '.php';

            if (!file_exists("$template")) {
                throw new \Exception("Could not found $tmpl");
            }
            ob_start(function ($buffer) {
                return preg_replace(
                    ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/' ],
                    [ '>', '<', '\\1', '' ],
                    $buffer
                );
            });

            include $template;
            $content = ob_get_clean();

            echo $content;
        }
    }