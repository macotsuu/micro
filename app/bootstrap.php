<?php
    spl_autoload_register(function (string $class) {
        $file = __DIR__ . '\\classes\\' . $class . '.php';
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
        
        if (file_exists($file)) {
            require_once $file;
        }
    });
    
    // setup config
    require_once __DIR__ . '/config/app.php';
    require_once __DIR__ . '/config/database.php';

    $app = new Micro();
    $routes = require_once __DIR__ . '/routes/api.php';
    $routes($app);

    return $app;
