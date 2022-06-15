<?php

use BoxyBird\Waffle\App;
use Illuminate\Cache\CacheManager;

App::getInstance()->singleton('cache', function ($app) {
    return new CacheManager($app);
});
