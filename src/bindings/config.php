<?php

use BoxyBird\Waffle\App;
use Illuminate\Config\Repository;

/**
 * Bind config instance to container
 */
App::getInstance()->singleton('config', function () {
    $files = [];

    foreach (glob(__DIR__ . '/../config/*.php') as $file) {
        $files[basename($file, '.php')] = require $file;
    }

    return new Repository($files);
});
