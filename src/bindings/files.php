<?php

use Illuminate\Filesystem\Filesystem;

/**
 * Bind files instance to container
 */
$app->singleton('files', function ($app) {
    return new Filesystem;
});
