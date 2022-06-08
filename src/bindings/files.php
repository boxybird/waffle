<?php

use Illuminate\Filesystem\Filesystem;

/**
 * Bind files instance to container
 */
$waffle_app->singleton('files', function ($waffle_app) {
    return new Filesystem;
});
