<?php

use Illuminate\Cache\CacheManager;

$app->singleton('cache', function () use ($app) {
    $app['config'] = $app->make('config')->get('cache');

    return new CacheManager($app);
});
