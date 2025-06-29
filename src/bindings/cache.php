<?php

use BoxyBird\Waffle\App;
use Illuminate\Cache\CacheManager;

App::getInstance()->singleton('cache', function ($app): CacheManager {
    if (!$app->get('db')->schema()->hasTable('waffle_cache')) {
        $app->get('db')->schema()->create('waffle_cache', function ($table): void {
            $table->string('key')->unique();
            $table->longText('value');
            $table->integer('expiration');
        });
    }

    return new CacheManager($app);
});
