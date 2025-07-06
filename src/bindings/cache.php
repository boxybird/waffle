<?php

use BoxyBird\Waffle\App;
use Illuminate\Cache\CacheManager;

App::getInstance()->singleton('cache', function ($app): CacheManager {
    global $wpdb;

    $table_name = $wpdb->prefix . 'waffle_cache';
    $cache_group = 'waffle_schema';
    $cache_key = 'waffle_cache_table_exists';

    if (wp_cache_get($cache_key, $cache_group) !== true) {
        if (!$app->get('db')->schema()->hasTable($table_name)) {
            $app->get('db')->schema()->create($table_name, function ($table): void {
                $table->string('key')->unique();
                $table->longText('value');
                $table->integer('expiration');
            });
        }

        wp_cache_set($cache_key, true, $cache_group);
    }

    return new CacheManager($app);
});