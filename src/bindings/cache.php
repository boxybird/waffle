<?php

use Illuminate\Cache\CacheManager;

$app->singleton('cache', function () use ($app) {
    return new CacheManager($app);
});
