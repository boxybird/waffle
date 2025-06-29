<?php

use BoxyBird\Waffle\App;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Bind files instance to container
 */
App::getInstance()->singleton('storage', fn($app): FilesystemManager => new FilesystemManager($app));
