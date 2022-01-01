<?php
    return function (Micro $app) {
        $app->get('/health', function (Context $ctx) {
            $ctx->json(['status' => 'ok']);
        });
    };