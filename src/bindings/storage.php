<?php

use BoxyBird\Waffle\App;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Bind files instance to container
 */
App::getInstance()->singleton('storage', function ($app) {
    return new FilesystemManager($app);
});
