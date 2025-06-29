<?php

use BoxyBird\Waffle\App;
use Illuminate\Filesystem\Filesystem;

/**
 * Bind files instance to container
 */
App::getInstance()->singleton('files', function (): Filesystem {
    return new Filesystem;
});
