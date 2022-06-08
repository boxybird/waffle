<?php

use Illuminate\Config\Repository;

/**
 * Bind config instance to container
 */
$waffle_app->singleton('config', function () {
    $files = [];

    foreach (glob(__DIR__ . '/../config/*.php') as $file) {
        $files[basename($file, '.php')] = require $file;
    }

    return new Repository($files);
});
