<?php

use BoxyBird\Waffle\App;
use Illuminate\Cache\CacheManager;

App::getInstance()->singleton('cache', function ($app): CacheManager {
    global $wpdb;

    $table_name = $wpdb->prefix.'waffle_cache';

    if (!$app->get('db')->schema()->hasTable($table_name)) {
        $app->get('db')->schema()->create($table_name, function ($table): void {
            $table->string('key')->unique();
            $table->longText('value');
            $table->integer('expiration');
        });
    }

    return new CacheManager($app);
});
