<?php

use Illuminate\Cache\CacheManager;

$waffle_app->singleton('cache', function () use ($waffle_app) {
    return new CacheManager($waffle_app);
});
