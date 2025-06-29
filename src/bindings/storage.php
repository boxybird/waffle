<?php

use BoxyBird\Waffle\App;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Bind files instance to container
 */
App::getInstance()->singleton('storage', function ($app): FilesystemManager {
    return new FilesystemManager($app);
});
