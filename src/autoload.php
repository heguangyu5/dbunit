<?php

spl_autoload_register(function ($class) {
    $prefix = 'PHPUnit\\DbUnit\\';
    $includePathDir = 'DbUnit/';

    // strlen($prefix) == 15
    if (strncmp($prefix, $class, 15) != 0) {
        return;
    }

    $relativeClass = substr($class, 15);
    $file = $includePathDir . str_replace('\\',  '/', $relativeClass) . '.php';

    $path = stream_resolve_include_path($file);
    if ($path) {
        include $path;
    }
});
